<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Utilities\StatusUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class StatusesQuery extends Query
{
    protected $attributes = [
        'name' => 'statuses',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return Type::listOf(Type::int());
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        return $this->guard()->check();
    }

    public function getAuthorizationMessage(): string
    {
        return trans('validation.unauthorized');
    }

    public function args(): array
    {
        return [
            'model' => [
                'type' => Type::string(),
                'defaultValue' => '',
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        if ($args['model']) {
            if (method_exists(new StatusUtility, $args['model'])) {
               return call_user_func_array([new StatusUtility, $args['model']], []);
            }
        }
        return StatusUtility::all();
    }
}
