<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\User;
use App\Utilities\ImageUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class UpdateUserMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateUser',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('User');
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        // true, if logged in
        return $this->guard()->check() && $this->guard()->id() === $args['id'];
    }

    public function rules(array $args = []): array
    {
        return [
            'id' => [
                'required',
                'string',
                'exists:users,id,deleted_at,NULL',
            ],
            'first_name' => [
                'required',
                'string',
            ],
            'last_name' => [
                'required',
                'string'
            ],
            'email' => [
                'required',
                'string',
                'email',
                Rule::unique('users', 'email')->ignore($args['id'])->whereNull('deleted_at')
            ],
            'image' => [
                'nullable',
                'string',
                'regex:/'.ImageUtility::getBase64ImageOrUrlImageRegex().'/i'
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
            'first_name' => [
                'name' => 'first_name',
                'type' => Type::nonNull(Type::string()),
            ],
            'last_name' => [
                'name' => 'last_name',
                'type' => Type::nonNull(Type::string()),
            ],
            'email' => [
                'name' => 'email',
                'type' => Type::nonNull(Type::string()),
            ],
            'image' => [
                'name' => 'image',
                'type' => Type::string(),
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var User $user */
        $user = User::find($args['id']);

        if ($args['image']) {
            $fileName = ImageUtility::changeImageProperty($args['image'], $user->image);

            if (!$fileName) {
                throw new \GraphQL\Error\Error(trans('mutation.image_failed'));
            }
            $user->image = $fileName;
        } elseif (!$args['image'] && $user->image) {
            $oldFileName = Str::of($user->image)->afterLast('/');

            ImageUtility::removeImage($oldFileName->__toString());
            $user->image = '';
        }

        $user->first_name = $args['first_name'];
        $user->last_name = $args['last_name'];
        $user->email = $args['email'];
        $user->save();

        return $user;
    }
}
