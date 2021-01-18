<?php
namespace App\Http\Controllers;


use App\Http\Requests\ResetLinkEmailRequest;
use App\Http\Requests\ResetRequest;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Utilities\ProxyRequest;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Http\Requests\LoginRequest;

/**
 * Class AuthController
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * @var ProxyRequest
     */
    protected $proxy;

    /**
     * AuthController constructor.
     * @param ProxyRequest $proxy
     */
    public function __construct(ProxyRequest $proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse|ValidationException
     * @throws ValidationException
     */
    public function login(LoginRequest $request)
    {
        /** @var User $user */
        $user = User::where($this->username(), $request->input($this->username()))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {

            throw ValidationException::withMessages([
                $this->username() => [trans('auth.failed')],
            ]);
        }

        $resp = $this->proxy->grantPasswordToken($request->input('email'), $request->input('password'));

        return response()->json([
            'expiresIn' => $resp->expires_in,
            'message' => 'You have been logged in',
        ], 200);
    }

    public function refreshToken()
    {
        $resp = $this->proxy->refreshAccessToken();

        return response()->json([
            'expiresIn' => $resp->expires_in,
            'message' => 'Token has been refreshed.',
        ], 200);
    }

    public function user()
    {
        return response()->json([
            'user' => Auth::guard('api')->user()->load(['roles', 'company'])
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse |\Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();

        cookie()->queue(cookie()->forget('refresh_token'));
        cookie()->queue(cookie()->forget('access_token'));

        return response()->json([
            'message' => 'You have been successfully logged out',
        ], 200);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'company_name' => ['required', 'string', 'unique:companies,name'],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8']
        ]);

        activity()->disableLogging();

        DB::transaction(function() use ($validatedData) {
            $company = Company::create([
                'name' => $validatedData['company_name'],
                'money' => 100000
            ]);

            $user = User::create([
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'email' => $validatedData['email'],
                'image' => '',
                'password' => Hash::make($validatedData['password']),
                'salary' => 0,
                'company_id' => $company->id
            ]);

            $roleOwner = Role::findByName('owner', 'api');
            $user->assignRole($roleOwner);

            $user->save();

            if (!$user || !$company) {
                throw new Exception(trans('validation.general_exception'));
            }
        });

        activity()->enableLogging();

        $resp = $this->proxy->grantPasswordToken($validatedData['email'], $validatedData['password']);

        return response()->json([
            'expiresIn' => $resp->expires_in,
            'message' => 'You have been registered.',
        ], 200);

    }

    /**
     * @param ResetLinkEmailRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(ResetLinkEmailRequest $request)
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => trans($status)
            ]);
        } else {
            return response()->json([
                'errors' => [
                    'email' => trans($status)
                ]
            ], 422);
        }
    }

    /**
     * @param ResetRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(ResetRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                $user->setRememberToken(Str::random(60));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json([
                'status' => trans($status),
            ]);
        } else {
            return response()->json([
                'errors' => [
                    'email' => trans($status)
                ]
            ], 422);
        }
    }

}
