<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Cargo;
use App\Rules\NotExistsRelationRule;
use App\Utilities\ImageUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class UpdateCargoMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateCargo',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('Cargo');
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
                'exists:cargos',
                new NotExistsRelationRule('Cargo', 'markets', 'update')
            ],
            'name' => [
                'required',
                'string'
            ],
            'adr' => [
                'required',
                'string',
                Rule::in(config('constants.trailer_adr'))
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
            'weight' => [
                'required',
                'min:1'
            ],
            'min_price' => [
                'required',
                'min:0'
            ],
            'max_price' => [
                'required',
                'min:0'
            ],
            'image' => [
                'required',
                'string',
                'regex:/'.ImageUtility::getBase64ImageOrUrlImageRegex().'/i'
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
            'engine_power' => [
                'name' => 'engine_power',
                'type' => Type::nonNull(Type::string()),
            ],
            'chassis' => [
                'name' => 'chassis',
                'type' => Type::nonNull(Type::string()),
            ],
            'adr' => [
                'name' => 'adr',
                'type' => Type::nonNull(Type::string()),
            ],
            'weight' => [
                'name' => 'weight',
                'type' => Type::nonNull(Type::string()),
            ],
            'min_price' => [
                'name' => 'min_price',
                'type' => Type::nonNull(Type::string()),
            ],
            'max_price' => [
                'name' => 'max_price',
                'type' => Type::nonNull(Type::string()),
            ],
            'image' => [
                'name' => 'image',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var Cargo $cargo */
        $cargo = Cargo::find($args['id']);

        $fileName = ImageUtility::changeImageProperty($args['image'], $cargo->image);

        if (!$fileName) {
            throw new \Exception(trans('mutation.image_failed'));
        }

        $cargo->name = $args['name'];
        $cargo->adr = $args['adr'];
        $cargo->weight = $args['weight'];
        $cargo->engine_power = $args['engine_power'];
        $cargo->chassis = $args['chassis'];
        $cargo->min_price = $args['min_price'];
        $cargo->max_price = $args['max_price'];
        $cargo->image = $fileName;

        $cargo->save();

        return $cargo;
    }
}
