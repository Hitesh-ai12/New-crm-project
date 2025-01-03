<?php

return [
    'supports_credentials' => true,
    'allowed_origins' => ['*'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'allowed_methods' => ['*'], // Make sure POST is allowed
    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    'allowed_headers' => ['Content-Type', 'X-Auth-Token', 'Authorization', 'X-Requested-With'],
    'exposed_headers' => [],
    'max_age' => 0,
];
