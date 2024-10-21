<?php

return [

    'default' => env('BROADCAST_DRIVER', 'null'),

    'connections' => [

        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'), // Your Pusher app key
            'secret' => env('PUSHER_APP_SECRET'), // Your Pusher app secret
            'app_id' => env('PUSHER_APP_ID'), // Your Pusher app ID
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER', 'ap2'), // Your Pusher cluster
                'useTLS' => true,
            ],
        ],

        // Other broadcasting connections (like Redis, etc.) can go here

    ],

];
