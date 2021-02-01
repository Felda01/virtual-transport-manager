<?php

declare(strict_types=1);

namespace App\GraphQL\Unions;

use App\Models\BankLoan;
use App\Models\Driver;
use App\Models\Garage;
use App\Models\Order;
use App\Models\Trailer;
use App\Models\Truck;
use App\Models\User;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\UnionType;

class SubjectUnion extends UnionType
{
    protected $attributes = [
        'name' => 'SubjectUnion',
    ];

    public function types(): array
    {
        return [
            GraphQL::type('Truck'),
            GraphQL::type('Trailer'),
            GraphQL::type('Driver'),
            GraphQL::type('Garage'),
            GraphQL::type('User'),
            GraphQL::type('BankLoan'),
            GraphQL::type('Order'),
        ];
    }

    public function resolveType($root)
    {
        if ($root instanceof Truck) {
            return GraphQL::type('Truck');
        } elseif ($root instanceof Trailer) {
            return GraphQL::type('Trailer');
        } elseif ($root instanceof Driver) {
            return GraphQL::type('Driver');
        } elseif ($root instanceof Garage) {
            return GraphQL::type('Garage');
        } elseif ($root instanceof User) {
            return GraphQL::type('User');
        } elseif ($root instanceof BankLoan) {
            return GraphQL::type('BankLoan');
        } elseif ($root instanceof Order) {
            return GraphQL::type('Order');
        }
        return null;
    }
}
