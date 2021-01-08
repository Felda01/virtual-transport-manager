<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\TruckModel;
use App\Utilities\ImageUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class CreateTruckModelMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createTruckModel',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('TruckModel');
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
                'string'
            ],
            'brand' => [
                'required',
                'string',
                Rule::in(config('constants.truck_brands'))
            ],
            "engine_power" => [
                'required',
                'integer',
                'min:1'
            ],
            'chassis' => [
                'required',
                'string',
                Rule::in(config('constants.truck_chassis'))
            ],
            'load' => [
                'required',
                'min:1'
            ],
            'emission_class' => [
                'required',
                Rule::in(config('constants.truck_emission_classes'))
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
                'regex:/'.ImageUtility::BASE64_IMAGE_REGEX.'/i'
            ],
            'price' => [
                'required',
                'min:1'
            ],
        ];
    }

    public function args(): array
    {
        return [
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
            ],
            'brand' => [
                'name' => 'brand',
                'type' => Type::nonNull(Type::string()),
            ],
            'engine_power' => [
                'name' => 'engine_power',
                'type' => Type::nonNull(Type::string()),
            ],
            'chassis' => [
                'name' => 'chassis',
                'type' => Type::nonNull(Type::string()),
            ],
            'load' => [
                'name' => 'load',
                'type' => Type::nonNull(Type::string()),
            ],
            'emission_class' => [
                'name' => 'emission_class',
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
        $fileName = ImageUtility::convertAndSaveBase64Image($args['image']);

        if (!$fileName) {
            throw new \Exception(trans('mutation.image_failed'));
        }

        $truckModel = TruckModel::create([
            'name' => $args['name'],
            'brand' => $args['brand'],
            'engine_power' => $args['engine_power'],
            'chassis' => $args['chassis'],
            'load' => $args['load'],
            'emission_class' => $args['emission_class'],
            'km' => $args['km'],
            'insurance' => $args['insurance'],
            'tax' => $args['tax'],
            'price' => $args['price'],
            'image' => Storage::disk('public')->url(ImageUtility::IMAGES_FOLDER . $fileName)
        ]);

        $truckModel->save();

        return $truckModel;
    }
}
