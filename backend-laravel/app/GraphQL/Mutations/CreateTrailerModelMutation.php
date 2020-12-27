<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\TrailerModel;
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

class CreateTrailerModelMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createTrailerModel',
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
        $fileName = ImageUtility::convertAndSaveBase64Image($args['image']);

        if (!$fileName) {
            throw new \Exception(trans('mutation.image_failed'));
        }

        $trailerModel = TrailerModel::create([
            'name' => $args['name'],
            'load' => $args['load'],
            'adr' => $args['adr'],
            'km' => $args['km'],
            'type' => $args['type'],
            'insurance' => $args['insurance'],
            'tax' => $args['tax'],
            'price' => $args['price'],
            'image' => Storage::disk('public')->url(ImageUtility::IMAGES_FOLDER . $fileName)
        ]);

        $trailerModel->save();

        return $trailerModel;
    }
}
