<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Events\ProcessTransaction;
use App\Events\RefreshQuery;
use App\Jobs\UpdateModelStatus;
use App\Models\Company;
use App\Models\Trailer;
use App\Models\TrailerModel;
use App\Rules\AvailableGarageSpotRule;
use App\Rules\ModelFromCompanyRule;
use App\Rules\MoneyCheckRule;
use App\Utilities\BroadcastUtility;
use App\Utilities\GameTimeUtility;
use App\Utilities\QueueJobUtility;
use App\Utilities\StatusUtility;
use App\Utilities\TransactionUtility;
use Carbon\Carbon;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class CreateTrailerMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createTrailer',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('Trailer');
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        // true, if logged in
        return $this->guard()->check() && $this->guard()->user()->hasPermissionTo(\App\Models\Permission::MANAGE_VEHICLES, \App\Models\Permission::GUARD);
    }

    public function rules(array $args = []): array
    {
        return [
            'trailer_model' => [
                'required',
                'exists:trailer_models,id,deleted_at,NULL',
            ],
            'garage' => [
                'required',
                'exists:garages,id,deleted_at,NULL',
                new ModelFromCompanyRule('Garage'),
                new AvailableGarageSpotRule('trailer')
            ],
            'general' => [
                new MoneyCheckRule('TrailerModel', $args['trailer_model'])
            ]
        ];
    }

    public function getAuthorizationMessage(): string
    {
        return trans('validation.unauthorized');
    }

    public function args(): array
    {
        return [
            'trailer_model' => [
                'name' => 'trailer_model',
                'type' => Type::nonNull(Type::string()),
            ],
            'garage' => [
                'name' => 'garage',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $company = Company::currentCompany();

        $result = DB::transaction(function () use ($args, $company) {
            /** @var TrailerModel $trailerModel */
            $trailerModel = TrailerModel::find($args['trailer_model']);

            /** @var Trailer $trailer */
            $trailer = Trailer::create([
                'trailer_model_id' => $args['trailer_model'],
                'company_id' => $company->id,
                'garage_id' => $args['garage'],
                'km' => $trailerModel->km,
                'status' => StatusUtility::DELIVERY_FROM_SHOP
            ]);

            $price = $trailerModel->price;

            $transaction = TransactionUtility::create($company, $trailer, -1 * $price, 'trailer_create');

            $oldMoney = $company->money;

            $company->decrement('money', $price);
            $company->save();

            if (!$trailer || !$transaction || $company->money !== ($oldMoney - $price)) {
                throw new \GraphQL\Error\Error(trans('validation.general_exception'));
            }

            return [
                'trailer' => $trailer,
                'garageId' => $args['garage']
            ];
        });

        BroadcastUtility::broadcast(new ProcessTransaction($company));
        BroadcastUtility::broadcast(new RefreshQuery($company, 'Trailer', $result['trailer']->id));
        BroadcastUtility::broadcast(new RefreshQuery($company, 'Garage', $result['garageId']));
        QueueJobUtility::dispatch(new UpdateModelStatus($result['trailer'], StatusUtility::IDLE), Carbon::parse(GameTimeUtility::gameTimeToRealTime(60 * 6), 'Europe/Bratislava'));
        return $result['trailer'];
    }
}
