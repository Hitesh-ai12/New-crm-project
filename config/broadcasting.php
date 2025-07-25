<?php

return [

    'default' => env('BROADCAST_DRIVER', 'pusher'),

    'connections' => [

            'pusher' => [
                'driver' => 'pusher',
                'key' => env('PUSHER_APP_KEY'),
                'secret' => env('PUSHER_APP_SECRET'),
                'app_id' => env('PUSHER_APP_ID'),
                'options' => [
                    'cluster' => env('PUSHER_APP_CLUSTER'),
                    'useTLS' => false,
                    'host' => env('PUSHER_HOST'),
                    'port' => env('PUSHER_PORT'),
                    'scheme' => env('PUSHER_SCHEME', 'http'),
                ],
            ],



        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],
    ],

];
