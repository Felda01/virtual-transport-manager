<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Events\RefreshMessageQuery;
use App\Models\Message;
use App\Rules\ModelFromCompanyRule;
use App\Utilities\BroadcastUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateMessageMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createMessage',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('Message');
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        // true, if logged in
        return $this->guard()->check() && in_array($this->guard()->id(), [$args['user1'], $args['user2']]);
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
            'message' => [
                'required',
                'string'
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
            'user1' => [
                'name' => 'user1',
                'type' => Type::nonNull(Type::string()),
            ],
            'user2' => [
                'name' => 'user2',
                'type' => Type::nonNull(Type::string()),
            ],
            'message' => [
                'name' => 'message',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $message = Message::create([
            'user_from' => $args['user1'],
            'user_to' => $args['user2'],
            'message' => $args['message']
        ]);

        BroadcastUtility::broadcast(new RefreshMessageQuery($args['user1'], $args['user2']));
        return $message;
    }
}
