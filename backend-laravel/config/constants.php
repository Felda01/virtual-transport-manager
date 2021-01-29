<?php

return [

    'adr' => [
        0,
        1,
        2,
        3,
        4,
        5,
        6
    ],

    'adr_price' => [
        1 =>  500,
        2 => 1000,
        3 => 2500,
        4 => 4000,
        5 => 7000,
        6 => 10000,
    ],

    'trailer_types' => [
        'FLB',
        'STD',
        'CNT',
        'LOG',
        'FDT',
        'LWB',
        'LLD',
    ],

    'truck_brands' => [
        'DAF',
        'IVECO',
        'MAN',
        'Mercedes-Benz',
        'Renault',
        'Scania',
        'Volvo',
    ],

    'truck_chassis' => [
        '4x2',
        '6x2',
        '6x4',
        '8x4',
    ],

    'truck_emission_classes' => [
        3,
        4,
        5,
        6,
    ],

    'road_trips' => [
        \App\Models\RoadTrip::SHORT,
        \App\Models\RoadTrip::MEDIUM,
        \App\Models\RoadTrip::LONG
    ]

];
