<?php
return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:5173', 'http://research.aastu.com'], // Update with your front-end URL
    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'allowed_credentials' => true, // Allow credentials (cookies, HTTP headers)

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
