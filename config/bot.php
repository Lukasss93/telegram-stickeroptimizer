<?php

return [

    'online' => (bool)env('BOT_ONLINE', true),
    'username' => env('BOT_USERNAME'),
    'privacy' => env('BOT_PRIVACY'),
    'localization' => env('BOT_LOCALIZATION'),
    'channel' => env('BOT_CHANNEL'),

    'rate_limit' => [
        'enabled' => env('BOT_RATE_LIMIT_ENABLED', false),
    ],
];
