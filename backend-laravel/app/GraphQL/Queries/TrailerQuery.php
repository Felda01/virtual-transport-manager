<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Trailer;
use App\Utilities\CompanyUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class TrailerQuery extends Query
{
    protected $attributes = [
        'name' => 'trailer',
        'description' => 'A query'
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
        $model = Trailer::find($args['id']);

        return $this->guard()->check() && CompanyUtility::can($model, $this->guard()->id());
    }

    public function getAuthorizationMessage(): string
    {
        return trans('validation.unauthorized');
    }

    public function rules(array $args = []): array
    {
        return [
            'id' => [
                'required',
                'string',
                'exists:trailers,id,deleted_at,NULL'
            ],
        ];
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::string(),
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Trailer::find($args['id']);
    }
}
