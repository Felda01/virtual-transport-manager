<?php

declare(strict_types=1);

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
                'locations' => \App\GraphQL\Queries\LocationsQuery::class,
                'availableLocations' => \App\GraphQL\Queries\AvailableLocationsQuery::class,
                'routes' => \App\GraphQL\Queries\RoutesQuery::class,
                'bankLoanTypes' => \App\GraphQL\Queries\BankLoanTypesQuery::class,
                'garageModels' => \App\GraphQL\Queries\GarageModelsQuery::class,

                'trailerModels' => \App\GraphQL\Queries\TrailerModelsQuery::class,
                'trailerTypes' => \App\GraphQL\Queries\TrailerTypesQuery::class,

                'ADRs' => \App\GraphQL\Queries\ADRsQuery::class,
                'chassis' => \App\GraphQL\Queries\ChassisQuery::class,

                'truckModels' => \App\GraphQL\Queries\TruckModelsQuery::class,
                'truckBrands' => \App\GraphQL\Queries\TruckBrandsQuery::class,
                'truckEmissionClasses' => \App\GraphQL\Queries\TruckEmissionClassesQuery::class,

                'cargos' => \App\GraphQL\Queries\CargosQuery::class,

                'adminDashboard' => \App\GraphQL\Queries\AdminDashboardQuery::class,

                'users' => \App\GraphQL\Queries\UsersQuery::class,
                'roles' => \App\GraphQL\Queries\RolesQuery::class,

                'user' => \App\GraphQL\Queries\UserQuery::class,
                'company' => \App\GraphQL\Queries\CompanyQuery::class,
                'me' => \App\GraphQL\Queries\MeQuery::class,

                'availableGarages' => \App\GraphQL\Queries\AvailableGaragesQuery::class,
                'countriesWithGarages' => \App\GraphQL\Queries\CountriesWithGaragesQuery::class,
                'availableGarageModelUpgrades' => \App\GraphQL\Queries\AvailableGarageModelUpgradesQuery::class,

                'availableDriversInGarage' => \App\GraphQL\Queries\AvailableDriversInGarageQuery::class,
                'availableTrailersInGarage' => \App\GraphQL\Queries\AvailableTrailersInGarageQuery::class,
                'availableTrucksInGarage' => \App\GraphQL\Queries\AvailableTrucksInGarageQuery::class,

                'garages' => \App\GraphQL\Queries\GaragesQuery::class,
                'trucks' => \App\GraphQL\Queries\TrucksQuery::class,
                'trailers' => \App\GraphQL\Queries\TrailersQuery::class,
                'drivers' => \App\GraphQL\Queries\DriversQuery::class,

                'statuses' => \App\GraphQL\Queries\StatusesQuery::class,

                'recruitmentAgencyDrivers' => \App\GraphQL\Queries\RecruitmentAgencyDriversQuery::class,
                'preferredRoadTrips' => \App\GraphQL\Queries\PreferredRoadTripsQuery::class,

                'garage' => \App\GraphQL\Queries\GarageQuery::class,
                'truck' => \App\GraphQL\Queries\TruckQuery::class,
                'trailer' => \App\GraphQL\Queries\TrailerQuery::class,
                'driver' => \App\GraphQL\Queries\DriverQuery::class,

                'activities' => \App\GraphQL\Queries\ActivitiesQuery::class,
                'markets' => \App\GraphQL\Queries\MarketsQuery::class,
                'transactions' => \App\GraphQL\Queries\TransactionsQuery::class,
                'nextPayment' => \App\GraphQL\Queries\NextPaymentQuery::class,
                'bankLoans' => \App\GraphQL\Queries\BankLoansQuery::class,

                'orders' => \App\GraphQL\Queries\OrdersQuery::class,
                'order' => \App\GraphQL\Queries\OrderQuery::class,

                'trucksForOrder' => \App\GraphQL\Queries\TrucksForOrderQuery::class,
                'pathsForOrder' => \App\GraphQL\Queries\PathsForOrderQuery::class,

                'conversation' => \App\GraphQL\Queries\ConversationQuery::class,

                'scoreBoard' => \App\GraphQL\Queries\ScoreBoardQuery::class,

                'dashboard' => \App\GraphQL\Queries\DashboardQuery::class,
            ],
            'mutation' => [
                // 'example_mutation'  => ExampleMutation::class,
                'createCountry' => \App\GraphQL\Mutations\CreateCountryMutation::class,
                'updateCountry' => \App\GraphQL\Mutations\UpdateCountryMutation::class,
                'deleteCountry' => \App\GraphQL\Mutations\DeleteCountryMutation::class,

                'createLocation' => \App\GraphQL\Mutations\CreateLocationMutation::class,
                'updateLocation' => \App\GraphQL\Mutations\UpdateLocationMutation::class,
                'deleteLocation' => \App\GraphQL\Mutations\DeleteLocationMutation::class,

                'createRoute' => \App\GraphQL\Mutations\CreateRouteMutation::class,
                'updateRoute' => \App\GraphQL\Mutations\UpdateRouteMutation::class,
                'deleteRoute' => \App\GraphQL\Mutations\DeleteRouteMutation::class,

                'createBankLoanType' => \App\GraphQL\Mutations\CreateBankLoanTypeMutation::class,
                'updateBankLoanType' => \App\GraphQL\Mutations\UpdateBankLoanTypeMutation::class,
                'deleteBankLoanType' => \App\GraphQL\Mutations\DeleteBankLoanTypeMutation::class,

                'createGarageModel' => \App\GraphQL\Mutations\CreateGarageModelMutation::class,
                'updateGarageModel' => \App\GraphQL\Mutations\UpdateGarageModelMutation::class,
                'deleteGarageModel' => \App\GraphQL\Mutations\DeleteGarageModelMutation::class,

                'createTrailerModel' => \App\GraphQL\Mutations\CreateTrailerModelMutation::class,
                'updateTrailerModel' => \App\GraphQL\Mutations\UpdateTrailerModelMutation::class,
                'deleteTrailerModel' => \App\GraphQL\Mutations\DeleteTrailerModelMutation::class,

                'createTruckModel' => \App\GraphQL\Mutations\CreateTruckModelMutation::class,
                'updateTruckModel' => \App\GraphQL\Mutations\UpdateTruckModelMutation::class,
                'deleteTruckModel' => \App\GraphQL\Mutations\DeleteTruckModelMutation::class,

                'createCargo' => \App\GraphQL\Mutations\CreateCargoMutation::class,
                'updateCargo' => \App\GraphQL\Mutations\UpdateCargoMutation::class,
                'deleteCargo' => \App\GraphQL\Mutations\DeleteCargoMutation::class,

                'createUser' => \App\GraphQL\Mutations\CreateUserMutation::class,
                'updateUser' => \App\GraphQL\Mutations\UpdateUserMutation::class,
                'deleteUser' => \App\GraphQL\Mutations\DeleteUserMutation::class,
                'updateUserPassword' => \App\GraphQL\Mutations\UpdateUserPasswordMutation::class,
                'updateUserSalary' => \App\GraphQL\Mutations\UpdateUserSalaryMutation::class,

                'createGarage' => \App\GraphQL\Mutations\CreateGarageMutation::class,
                'updateGarage' => \App\GraphQL\Mutations\UpdateGarageMutation::class,
                'deleteGarage' => \App\GraphQL\Mutations\DeleteGarageMutation::class,

                'createTruck' => \App\GraphQL\Mutations\CreateTruckMutation::class,
                'deleteTruck' => \App\GraphQL\Mutations\DeleteTruckMutation::class,

                'createTrailer' => \App\GraphQL\Mutations\CreateTrailerMutation::class,
                'deleteTrailer' => \App\GraphQL\Mutations\DeleteTrailerMutation::class,

                'createDriver' => \App\GraphQL\Mutations\CreateDriverMutation::class,
                'updateDriver' => \App\GraphQL\Mutations\UpdateDriverMutation::class,
                'deleteDriver' => \App\GraphQL\Mutations\DeleteDriverMutation::class,
                'updateDriverLocation' => \App\GraphQL\Mutations\UpdateDriverLocationMutation::class,

                'assignDriverToTruck' => \App\GraphQL\Mutations\AssignDriverToTruckMutation::class,
                'assignTrailerToTruck' => \App\GraphQL\Mutations\AssignTrailerToTruckMutation::class,

                'unassignDriverFromTruck' => \App\GraphQL\Mutations\UnassignDriverFromTruckMutation::class,
                'unassignTrailerFromTruck' => \App\GraphQL\Mutations\UnassignTrailerFromTruckMutation::class,

                'createOrder' => \App\GraphQL\Mutations\CreateOrderMutation::class,
                'updateOrder' => \App\GraphQL\Mutations\UpdateOrderMutation::class,

                'createBankLoan' => \App\GraphQL\Mutations\CreateBankLoanMutation::class,
                'updateBankLoan' => \App\GraphQL\Mutations\UpdateBankLoanMutation::class,

                'createMessage' => \App\GraphQL\Mutations\CreateMessageMutation::class,
            ],
            'middleware' => ['auth:api'],
            'method' => ['get', 'post'],
        ],
    ],

    // The types available in the application.
    'types' => [
        // \Rebing\GraphQL\Support\UploadType::class,
        'Activity' => \App\GraphQL\Types\ActivityType::class,
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
        'Permission' => \App\GraphQL\Types\PermissionType::class,
        'RoadTrip' => \App\GraphQL\Types\RoadTripType::class,
        'Role' => \App\GraphQL\Types\RoleType::class,
        'Route' => \App\GraphQL\Types\RouteType::class,
        'TrailerModel' => \App\GraphQL\Types\TrailerModelType::class,
        'Trailer' => \App\GraphQL\Types\TrailerType::class,
        'Transaction' => \App\GraphQL\Types\TransactionType::class,
        'TruckModel' =>  \App\GraphQL\Types\TruckModelType::class,
        'Truck' => \App\GraphQL\Types\TruckType::class,
        'User' => \App\GraphQL\Types\UserType::class,

        'TranslationInput' => \App\GraphQL\Inputs\TranslationInput::class,
        'FilterInput' => \App\GraphQL\Inputs\FilterInput::class,

        'ProductableUnion' => \App\GraphQL\Unions\ProductableUnion::class,
        'SubjectUnion' => \App\GraphQL\Unions\SubjectUnion::class,
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
