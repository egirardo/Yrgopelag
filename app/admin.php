<?php

declare(strict_types=1);

return [

    //figure out what to add to admin page for easy adjustments on website - more private storage add to gitignore

    'economy_pricing' => 2,
    'basic_pricing' => 5,
    'premium_pricing' => 10,
    'superior_pricing' => 17,

    'room_prices' => [
        'budget' => 5,
        'standard' => 8,
        'luxury' => 11
    ],

    'booked' => [],

    'featureGrid' => [
        'water' => [
            'economy' => 'pool',
            'basic' => 'scuba diving',
            'premium' => 'olympic pool',
            'superior' => 'waterpark with fire and minibar'
        ],
        'games' => [
            'economy' => 'yahtzee',
            'basic' => 'ping pong table',
            'premium' => 'PS5',
            'superior' => 'casino'
        ],
        'wheels' => [
            'economy' => 'unicycle',
            'basic' => 'bicycle',
            'premium' => 'trike',
            'superior' => 'four-wheeled motorized beast'
        ],
        'hotel-specific' => [
            'economy' => 'custom-1',
            'basic' => 'custom-2',
            'premium' => 'custom-3',
            'superior' => 'custom-4'
        ],
    ],

    'today' => date('Y-m-d')
];
