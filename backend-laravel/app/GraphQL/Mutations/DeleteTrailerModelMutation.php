<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\TrailerModel;
use App\Rules\NotExistsRelationRule;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class DeleteTrailerModelMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteTrailerModel',
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
        return $this->guard()->check() && $this->guard()->user()->hasRole('admin');
    }

    public function rules(array $args = []): array
    {
        return [
            'id' => [
                'required',
                'string',
                'exists:trailer_models',
                new NotExistsRelationRule('TrailerModel', 'trailers')
            ]
        ];
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
        /** @var TrailerModel $trailerModel */
        $trailerModel = TrailerModel::find($args['id']);

        $deletedTrailerModel = $trailerModel;

        try {
            $trailerModel->delete();
        } catch (\Exception $e) {
        }

        return $deletedTrailerModel;
    }
}
