<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Company;
use App\Models\Trailer;
use App\Rules\CanDeleteTrailerRule;
use App\Rules\ModelFromCompanyRule;
use App\Rules\ModelStatusRule;
use App\Rules\TrailerEmptyTruckSpotRule;
use App\Utilities\StatusUtility;
use App\Utilities\TransactionUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class DeleteTrailerMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteTrailer',
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
            'id' => [
                'required',
                'string',
                'exists:trailers,id,deleted_at,NULL',
                new ModelFromCompanyRule('Trailer'),
                new ModelStatusRule('Trailer', [StatusUtility::IDLE]),
                new CanDeleteTrailerRule()
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
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $result = DB::transaction(function () use ($args) {
            /** @var Trailer $trailer */
            $trailer = Trailer::find($args['id']);

            $deletedTrailer = $trailer;

            $company = Company::currentCompany();

            $price = $deletedTrailer->trailerModel->price / 2;

            $transaction = TransactionUtility::create($company, $trailer, $price, 'sell');

            try {
                $trailer->delete();
            } catch (\Exception $e) {
            }

            $oldMoney = $company->money;

            $company->increment('money', $price);
            $company->save();

            if (!$trailer->trashed() || !$transaction || $company->money !== $oldMoney + $price) {
                throw new \Exception(trans('validation.general_exception'));
            }

            return [
                'trailer' => $deletedTrailer,
                'transaction' => $transaction
            ];
        });

        return $result['trailer'];
    }
}
