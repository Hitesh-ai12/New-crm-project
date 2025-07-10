<?php

use BeyondCode\LaravelWebSockets\Dashboard\Http\Middleware\Authorize;

return [

    /*
    |--------------------------------------------------------------------------
    | Dashboard Settings
    |--------------------------------------------------------------------------
    |
    | Here you may configure the dashboard settings that are used by
    | Beyondcode's Laravel WebSockets package.
    |
    */

    'dashboard' => [
        'middleware' => [
            'web',
            Authorize::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | WebSockets Route
    |--------------------------------------------------------------------------
    |
    | This package will register a route that points to the WebSocket server.
    | You can customize the URI of that route here.
    |
    */

    'path' => 'laravel-websockets',

    /*
    |--------------------------------------------------------------------------
    | WebSockets Settings
    |--------------------------------------------------------------------------
    |
    | Here you can customize the WebSocket server behavior.
    |
    */

    'middleware' => [
        'web',
    ],

    'statistics' => [
        'model' => \BeyondCode\LaravelWebSockets\Statistics\Models\WebSocketsStatisticsEntry::class,
        'interval_in_seconds' => 60,
    ],

    'apps' => [
        [
            'id' => env('PUSHER_APP_ID'),
            'name' => env('APP_NAME', 'Laravel'),
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'path' => '',
            'capacity' => null,
            'enable_client_messages' => true,
            'enable_statistics' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SSL Configuration
    |--------------------------------------------------------------------------
    |
    | By default, the WebSocket server will run using HTTP. If you want to
    | use HTTPS, provide the path to your SSL certificate and private key.
    |
    */

    'ssl' => [
        'local_cert' => null,
        'local_pk' => null,
        'passphrase' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Channel Manager
    |--------------------------------------------------------------------------
    |
    | This class manages the channel memory. You can use a different
    | implementation if you want to store channels in Redis, etc.
    |
    */

    'channel_manager' => \BeyondCode\LaravelWebSockets\ChannelManagers\ArrayChannelManager::class,
];
