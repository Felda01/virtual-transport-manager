<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\TrailerModel;
use App\Rules\NotExistsRelationRule;
use App\Utilities\ImageUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class UpdateTrailerModelMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateTrailerModel',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('TrailerModel');
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        // true, if logged in
        return $this->guard()->check() && $this->guard()->user()->hasPermissionTo(\App\Models\Permission::MANAGE_STATIC, \App\Models\Permission::GUARD);
    }

    public function rules(array $args = []): array
    {
        return [
            'id' => [
                'required',
                'string',
                'exists:trailer_models,id,deleted_at,NULL',
                new NotExistsRelationRule('TrailerModel', 'trailers', 'update')
            ],
            'name' => [
                'required',
                'string'
            ],
            'type' => [
                'required',
                'string',
                Rule::in(config('constants.trailer_types'))
            ],
            'load' => [
                'required',
                'min:1'
            ],
            'adr' => [
                'required',
                'string',
                Rule::in(config('constants.trailer_adr'))
            ],
            'km' => [
                'required',
                'min:0'
            ],
            'insurance' => [
                'required',
                'min:1'
            ],
            'tax' => [
                'required',
                'min:1'
            ],
            'image' => [
                'required',
                'string',
                'regex:/' . ImageUtility::getBase64ImageOrUrlImageRegex() . '/i'
            ],
            'price' => [
                'required',
                'min:1'
            ],
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
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
            ],
            'type' => [
                'name' => 'type',
                'type' => Type::nonNull(Type::string()),
            ],
            'load' => [
                'name' => 'load',
                'type' => Type::nonNull(Type::string()),
            ],
            'adr' => [
                'name' => 'adr',
                'type' => Type::nonNull(Type::string()),
            ],
            'km' => [
                'name' => 'km',
                'type' => Type::nonNull(Type::string()),
            ],
            'insurance' => [
                'name' => 'insurance',
                'type' => Type::nonNull(Type::string()),
            ],
            'tax' => [
                'name' => 'tax',
                'type' => Type::nonNull(Type::string()),
            ],
            'image' => [
                'name' => 'image',
                'type' => Type::nonNull(Type::string()),
            ],
            'price' => [
                'name' => 'price',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var TrailerModel $trailerModel */
        $trailerModel = TrailerModel::find($args['id']);

        $fileName = ImageUtility::changeImageProperty($args['image'], $trailerModel->image);

        if (!$fileName) {
            throw new \Exception(trans('mutation.image_failed'));
        }

        $trailerModel->name = $args['name'];
        $trailerModel->type = $args['type'];
        $trailerModel->load = $args['load'];
        $trailerModel->adr = $args['adr'];
        $trailerModel->km = $args['km'];
        $trailerModel->price = $args['price'];
        $trailerModel->insurance = $args['insurance'];
        $trailerModel->tax = $args['tax'];
        $trailerModel->image = $fileName;

        $trailerModel->save();

        return $trailerModel;
    }
}
