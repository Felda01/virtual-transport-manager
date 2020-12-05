<?php

declare(strict_types=1);

use example\Mutation\ExampleMutation;
use example\Query\ExampleQuery;
use example\Type\ExampleRelationType;
use example\Type\ExampleType;

return [

    // The prefix for routes
    'prefix' => 'api/graphql',

    // The routes to make GraphQL request. Either a string that will apply
    // to both query and mutation or an array containing the key 'query' and/or
    // 'mutation' with the according Route
    //
    // Example:
    //
    // Same route for both query and mutation
    //
    // 'routes' => 'path/to/query/{graphql_schema?}',
    //
    // or define each route
    //
    // 'routes' => [
    //     'query' => 'query/{graphql_schema?}',
    //     'mutation' => 'mutation/{graphql_schema?}',
    // ]
    //
    'routes' => '{graphql_schema?}',

    // The controller to use in GraphQL request. Either a string that will apply
    // to both query and mutation or an array containing the key 'query' and/or
    // 'mutation' with the according Controller and method
    //
    // Example:
    //
    // 'controllers' => [
    //     'query' => '\Rebing\GraphQL\GraphQLController@query',
    //     'mutation' => '\Rebing\GraphQL\GraphQLController@mutation'
    // ]
    //
    'controllers' => \Rebing\GraphQL\GraphQLController::class.'@query',

    // Any middleware for the graphql route group
    'middleware' => [],

    // Additional route group attributes
    //
    // Example:
    //
    'route_group_attributes' => [],
    //
    //'route_group_attributes' => [],

    // The name of the default schema used when no argument is provided
    // to GraphQL::schema() or when the route is used without the graphql_schema
    // parameter.
    'default_schema' => 'default',

    // The schemas for query and/or mutation. It expects an array of schemas to provide
    // both the 'query' fields and the 'mutation' fields.
    //
    // You can also provide a middleware that will only apply to the given schema
    //
    // Example:
    //
    //  'schema' => 'default',
    //
    //  'schemas' => [
    //      'default' => [
    //          'query' => [
    //              'users' => 'App\GraphQL\Query\UsersQuery'
    //          ],
    //          'mutation' => [
    //
    //          ]
    //      ],
    //      'user' => [
    //          'query' => [
    //              'profile' => 'App\GraphQL\Query\ProfileQuery'
    //          ],
    //          'mutation' => [
    //
    //          ],
    //          'middleware' => ['auth'],
    //      ],
    //      'user/me' => [
    //          'query' => [
    //              'profile' => 'App\GraphQL\Query\MyProfileQuery'
    //          ],
    //          'mutation' => [
    //
    //          ],
    //          'middleware' => ['auth'],
    //      ],
    //  ]
    //
    'schemas' => [
        'default' => [
            'query' => [
                // 'example_query' => ExampleQuery::class,
                'locales' => \App\GraphQL\Queries\LocalesQuery::class,
                'countries' => \App\GraphQL\Queries\CountriesQuery::class,
            ],
            'mutation' => [
                // 'example_mutation'  => ExampleMutation::class,
                'createCountry' => \App\GraphQL\Mutations\CreateCountryMutation::class,
                'updateCountry' => \App\GraphQL\Mutations\UpdateCountryMutation::class,
                'deleteCountry' => \App\GraphQL\Mutations\DeleteCountryMutation::class
            ],
            'middleware' => ['auth:api'],
            'method' => ['get', 'post'],
        ],
//        'auth' => [
//            'query' => [
//                'countries' => \App\GraphQL\Queries\CountriesQuery::class,
//                // 'example_query' => ExampleQuery::class,
//            ],
//            'mutation' => [
//                // 'example_mutation'  => ExampleMutation::class,
//            ],
//            'middleware' => ['auth:api'],
//            'method' => ['get', 'post'],
//        ],
    ],

    // The types available in the application.
    'types' => [
        // \Rebing\GraphQL\Support\UploadType::class,
        'BankLoan' => \App\GraphQL\Types\BankLoanType::class,
        'BankLoanType' => \App\GraphQL\Types\BankLoanTypeType::class,
        'Cargo' => \App\GraphQL\Types\CargoType::class,
        'Company' => \App\GraphQL\Types\CompanyType::class,
        'Constant' => \App\GraphQL\Types\ConstantType::class,
        'Contract' => \App\GraphQL\Types\ContractType::class,
        'Country' => \App\GraphQL\Types\CountryType::class,
        'Customer' => \App\GraphQL\Types\CustomerType::class,
        'Driver' => \App\GraphQL\Types\DriverType::class,
        'GarageModel' => \App\GraphQL\Types\GarageModelType::class,
        'Garage' => \App\GraphQL\Types\GarageType::class,
        'LanguageLine' => \App\GraphQL\Types\LanguageLineType::class,
        'Location' => \App\GraphQL\Types\LocationType::class,
        'Market' => \App\GraphQL\Types\MarketType::class,
        'Message' => \App\GraphQL\Types\MessageType::class,
        'Notification' => \App\GraphQL\Types\NotificationType::class,
        'Order' => \App\GraphQL\Types\OrderType::class,
        'RoadTrip' => \App\GraphQL\Types\RoadTripType::class,
        'Role' => \App\GraphQL\Types\RoleType::class,
        'Route' => \App\GraphQL\Types\RouteType::class,
        'TrailerModel' => \App\GraphQL\Types\TrailerModelType::class,
        'Trailer' => \App\GraphQL\Types\TrailerType::class,
        'Transaction' => \App\GraphQL\Types\TransactionType::class,
        'TruckModel' =>  \App\GraphQL\Types\TruckModelType::class,
        'Truck' => \App\GraphQL\Types\TruckType::class,
        'User' => \App\GraphQL\Types\UserType::class,

        'TranslationInput' => \App\GraphQL\Inputs\TranslationInput::class
    ],

    // The types will be loaded on demand. Default is to load all types on each request
    // Can increase performance on schemes with many types
    // Presupposes the config type key to match the type class name property
    'lazyload_types' => true,

    // This callable will be passed the Error object for each errors GraphQL catch.
    // The method should return an array representing the error.
    // Typically:
    // [
    //     'message' => '',
    //     'locations' => []
    // ]
    'error_formatter' => ['\Rebing\GraphQL\GraphQL', 'formatError'],

    /*
     * Custom Error Handling
     *
     * Expected handler signature is: function (array $errors, callable $formatter): array
     *
     * The default handler will pass exceptions to laravel Error Handling mechanism
     */
    'errors_handler' => ['\Rebing\GraphQL\GraphQL', 'handleErrors'],

    // You can set the key, which will be used to retrieve the dynamic variables
    'params_key' => 'variables',

    /*
     * Options to limit the query complexity and depth. See the doc
     * @ https://webonyx.github.io/graphql-php/security
     * for details. Disabled by default.
     */
    'security' => [
        'query_max_complexity' => null,
        'query_max_depth' => null,
        'disable_introspection' => true,
    ],

    /*
     * You can define your own pagination type.
     * Reference \Rebing\GraphQL\Support\PaginationType::class
     */
    'pagination_type' => \Rebing\GraphQL\Support\PaginationType::class,

    /*
     * Config for GraphiQL (see (https://github.com/graphql/graphiql).
     */
    'graphiql' => [
        'prefix' => '/graphiql',
        'controller' => \Rebing\GraphQL\GraphQLController::class.'@graphiql',
        'middleware' => [],
        'view' => 'graphql::graphiql',
        'display' => env('ENABLE_GRAPHIQL', true),
    ],

    /*
     * Overrides the default field resolver
     * See http://webonyx.github.io/graphql-php/data-fetching/#default-field-resolver
     *
     * Example:
     *
     * ```php
     * 'defaultFieldResolver' => function ($root, $args, $context, $info) {
     * },
     * ```
     * or
     * ```php
     * 'defaultFieldResolver' => [SomeKlass::class, 'someMethod'],
     * ```
     */
    'defaultFieldResolver' => null,

    /*
     * Any headers that will be added to the response returned by the default controller
     */
    'headers' => [],

    /*
     * Any JSON encoding options when returning a response from the default controller
     * See http://php.net/manual/function.json-encode.php for the full list of options
     */
    'json_encoding_options' => 0,
];
