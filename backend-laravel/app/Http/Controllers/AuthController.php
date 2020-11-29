<?php
namespace App\Http\Controllers;


use App\Models\User;
use App\Utilities\ProxyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

//    /**
//     * @param ResetLinkEmailRequest $request
//     * @return \Illuminate\Http\JsonResponse
//     */
//    public function sendResetLinkEmail(ResetLinkEmailRequest $request)
//    {
//        $response = $this->broker()->sendResetLink(
//            $request->only('email')
//        );
//
//        if ($response == Password::RESET_LINK_SENT) {
//            return response()->json([
//                'status' => trans($response)
//            ]);
//        } else {
//            return response()->json([
//                'email' => trans($response)
//            ], 422);
//        }
//    }
//
//    /**
//     * @param ResetRequest $request
//     * @return \Illuminate\Http\JsonResponse
//     */
//    public function reset(ResetRequest $request)
//    {
//        $response = $this->broker()->reset(
//            $request->only(['email', 'password', 'password_confirmation', 'token']), function ($user, $password) {
//                $user->password = Hash::make($password);
//                $user->setRememberToken(Str::random(60));
//                $user->save();
//            }
//        );
//
//        if ($response == Password::PASSWORD_RESET) {
//            return response()->json([
//                'status' => trans($response)
//            ]);
//        } else {
//            return response()->json([
//                'email' => trans($response)
//            ], 422);
//        }
//    }
//
//    /**
//     * Get the broker to be used during password reset.
//     *
//     * @return \Illuminate\Contracts\Auth\PasswordBroker
//     */
//    public function broker()
//    {
//        return Password::broker();
//    }
}
