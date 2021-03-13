<?php

return [

    'dev' => [
        'id' => (int) env('DEV_ID'),
        'name' => env('DEV_NAME'),
        'username' => env('DEV_USERNAME'),
        'email' => env('DEV_EMAIL'),
        'website' => env('DEV_WEBSITE'),
    ],

    'bot' => [
        'name' => env('BOT_NAME', env('APP_NAME', 'Telegram Bot')),
        'username' => env('BOT_USERNAME'),
        'version' => env('BOT_VERSION'),
        'source' => env('BOT_SOURCE'),
        'changelog' => env('BOT_CHANGELOG'),
        'privacy' => env('BOT_PRIVACY'),
        'commands' => [
            ['command' => 'start', 'description' => 'Welcome message'],
            ['command' => 'help', 'description' => 'Help message'],
            ['command' => 'privacy', 'description' => 'Privacy Policy'],
            ['command' => 'about', 'description' => 'About the bots'],
        ],
        'online' => (bool) env('BOT_ONLINE', true),
    ],

];
