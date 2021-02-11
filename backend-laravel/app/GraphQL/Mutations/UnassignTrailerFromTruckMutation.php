<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Trailer;
use App\Models\Truck;
use App\Rules\ModelFromCompanyRule;
use App\Rules\ModelsInGarageRule;
use App\Rules\ModelStatusRule;
use App\Rules\TrailerHasTruckRule;
use App\Rules\TruckHasTrailerRule;
use App\Utilities\StatusUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UnassignTrailerFromTruckMutation extends Mutation
{
    protected $attributes = [
        'name' => 'unassignTrailerFromTruck',
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

    public function getAuthorizationMessage(): string
    {
        return trans('validation.unauthorized');
    }

    public function rules(array $args = []): array
    {
        return [
            'trailer' => [
                'required',
                'string',
                'exists:trailers,id,deleted_at,NULL',
                new ModelFromCompanyRule('Trailer'),
                new ModelStatusRule('Trailer', [StatusUtility::ON_DUTY]),
                new TrailerHasTruckRule($args['truck'])
            ],
            'truck' => [
                'required',
                'string',
                'exists:trucks,id,deleted_at,NULL',
                new ModelFromCompanyRule('Truck'),
                new ModelStatusRule('Truck', [StatusUtility::ON_DUTY, StatusUtility::IDLE]),
                new TruckHasTrailerRule($args['trailer'])
            ],
            'general' => [
                new ModelsInGarageRule('Trailer', $args['trailer'], 'Truck', $args['truck']),
            ]
        ];
    }

    public function args(): array
    {
        return [
            'trailer' => [
                'name' => 'trailer',
                'type' => Type::nonNull(Type::string()),
            ],
            'truck' => [
                'name' => 'truck',
                'type' => Type::nonNull(Type::string()),
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $result = DB::transaction(function() use($args) {
            /** @var Trailer $trailer */
            $trailer = Trailer::find($args['trailer']);

            /** @var Truck $truck */
            $truck = Truck::find($args['truck']);

            $truck->trailer()->dissociate();

            if ($truck->drivers()->exists()) {
                $truck->drivers()->update([
                    'status' => StatusUtility::IDLE
                ]);
            }
            $truckSaved = $truck->save();

            $trailer->status = StatusUtility::IDLE;
            $trailerSaved = $trailer->save();

            if (!$trailerSaved || !$truckSaved) {
                throw new \GraphQL\Error\Error(trans('validation.general_exception'));
            }

            return [
                'trailer' => $trailer
            ];
        });

        return $result['trailer'];
    }
}
