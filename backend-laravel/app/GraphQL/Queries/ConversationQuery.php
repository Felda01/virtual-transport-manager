<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Message;
use App\Rules\ModelFromCompanyRule;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class ConversationQuery extends Query
{
    protected $attributes = [
        'name' => 'conversation',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('Message');
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        return $this->guard()->check() && in_array($this->guard()->id(), [$args['user1'], $args['user2']]);
    }

    public function getAuthorizationMessage(): string
    {
        return trans('validation.unauthorized');
    }

    public function rules(array $args = []): array
    {
        return [
            'user1' => [
                'required',
                'string',
                'exists:users,id,deleted_at,NULL',
                new ModelFromCompanyRule('User')
            ],
            'user2' => [
                'required',
                'string',
                'exists:users,id,deleted_at,NULL',
                new ModelFromCompanyRule('User')
            ],
        ];
    }

    public function args(): array
    {
        return [
            'user1' => [
                'type' => Type::string(),
            ],
            'user2' => [
                'type' => Type::string(),
            ],
            'limit' => [
                'type' => Type::int(),
                'defaultValue' => 50,
            ],
            'page' => [
                'type' => Type::int(),
                'defaultValue' => 1,
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var SelectFields $fields */
        $fields = $getSelectFields();
        $select = $fields->getSelect();
        $with = $fields->getRelations();

        $query = Message::query();

        $query = $query->where(function($query) use ($args) {
            $query->where('user_from', $args['user1'])
                ->where('user_to', $args['user2']);
        })->orWhere(function ($query) use ($args) {
            $query->where('user_from', $args['user2'])
                ->where('user_to', $args['user1']);
        });

        return $query->with($with)
            ->select($select)
            ->orderBy('created_at', 'desc')
            ->paginate($args['limit'], ['*'], 'page', $args['page']);
    }
}
