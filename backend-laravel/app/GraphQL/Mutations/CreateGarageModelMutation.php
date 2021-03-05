<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\GarageModel;
use App\Utilities\ImageUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class CreateGarageModelMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createGarageModel',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('GarageModel');
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
            'name' => [
                'required',
                'string',
                'unique:garage_models,name,NULL,id,deleted_at,NULL'
            ],
            'truck_count' => [
                'required',
                'min:1'
            ],
            'trailer_count' => [
                'required',
                'min:1'
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
//                'regex:/'.ImageUtility::BASE64_IMAGE_REGEX.'/i'
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
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
            ],
            'truck_count' => [
                'name' => 'truck_count',
                'type' => Type::nonNull(Type::string()),
            ],
            'trailer_count' => [
                'name' => 'trailer_count',
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
//        $fileName = ImageUtility::convertAndSaveBase64Image($args['image']);
//
//        if (!$fileName) {
//            throw new \GraphQL\Error\Error(trans('mutation.image_failed'));
//        }

        $garageModel = GarageModel::create([
            'name' => $args['name'],
            'truck_count' => $args['truck_count'],
            'trailer_count' => $args['trailer_count'],
            'insurance' => $args['insurance'],
            'tax' => $args['tax'],
            'price' => $args['price'],
//            'image' => Storage::disk('public')->url(ImageUtility::IMAGES_FOLDER . $fileName)
            'image' => $args['image']
        ]);
        $garageModel->save();

        return $garageModel;
    }
}
