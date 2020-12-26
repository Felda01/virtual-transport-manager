<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\GarageModel;
use App\Rules\NotExistsRelationRule;
use App\Utilities\ImageUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class UpdateGarageModelMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateGarageModel',
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
        return $this->guard()->check() && $this->guard()->user()->hasRole('admin');
    }

    public function rules(array $args = []): array
    {
        return [
            'id' => [
                'required',
                'string',
                'exists:garage_models',
                new NotExistsRelationRule('GarageModel', 'garages', 'update')
            ],
            'name' => [
                'required',
                'string',
                Rule::unique('garage_models', 'name')->ignore($args['id'])
            ],
            'truck_count' => [
                'required',
                'integer',
                'min:1'
            ],
            'trailer_count' => [
                'required',
                'integer',
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
                'regex:/' . ImageUtility::getBase64ImageOrUrlImageRegex() . '/i'
            ],
        ];
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
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var GarageModel $garageModel */
        $garageModel = GarageModel::find($args['id']);

        $fileName = ImageUtility::changeImageProperty($args['image'], $garageModel->image);

        if (!$fileName) {
            throw new \Exception(trans('mutation.image_failed'));
        }

        $garageModel->name = $args['name'];
        $garageModel->truck_count = $args['truck_count'];
        $garageModel->trailer_count = $args['trailer_count'];
        $garageModel->insurance = $args['insurance'];
        $garageModel->tax = $args['tax'];
        $garageModel->image = $fileName;

        $garageModel->save();

        return $garageModel;
    }
}
