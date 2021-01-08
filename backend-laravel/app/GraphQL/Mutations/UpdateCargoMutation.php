<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Cargo;
use App\Rules\NotExistsRelationRule;
use App\Rules\UniqueTranslationRule;
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
        return $this->guard()->check() && $this->guard()->user()->hasPermissionTo(\App\Models\Permission::MANAGE_STATIC, \App\Models\Permission::GUARD);
    }

    public function rules(array $args = []): array
    {
        return [
            'id' => [
                'required',
                'string',
                'exists:cargos,id,deleted_at,NULL',
                new NotExistsRelationRule('Cargo', 'markets', 'update')
            ],
            'name_translations.*.value' => [
                'required',
                'string',
            ],
            'name_translations.*.locale' => [
                'required',
                'string',
                Rule::in(config('translatable.available_locales'))
            ],
            'name_translations.*' => [
                new UniqueTranslationRule('cargos', 'name', $args['id'])
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
            'name_translations' => [
                'name' => 'name_translations',
                'type' => Type::nonNull(Type::listOf(GraphQL::type('TranslationInput'))),
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

        $nameTranslations = [];

        foreach ($args['name_translations'] as $nameTranslation) {
            $nameTranslations[$nameTranslation['locale']] = $nameTranslation['value'];
        }

        $cargo->setTranslations('name', $nameTranslations);
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
