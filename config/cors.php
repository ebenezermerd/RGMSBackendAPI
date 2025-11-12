<?php
return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:5173'], // Update with your front-end URL
    'allowed_origins_patterns' => ['/^http:\/\/(127\.0\.0\.1|localhost)(:\\d+)?$/'],

    'allowed_headers' => ['*'],

    'allowed_credentials' => true, // Allow credentials (cookies, HTTP headers)

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
