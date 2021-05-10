<?php

return [

    'online' => (bool) env('BOT_ONLINE', true),
    'name' => env('BOT_NAME', env('APP_NAME', 'Telegram Bot')),
    'username' => env('BOT_USERNAME'),
    'version' => env('BOT_VERSION'),
    'source' => env('BOT_SOURCE'),
    'changelog' => env('BOT_CHANGELOG'),
    'privacy' => env('BOT_PRIVACY'),

    'commands' => [
        ['command' => 'start', 'description' => 'Welcome message'],
        ['command' => 'help', 'description' => 'Help message'],
        ['command' => 'donate', 'description' => 'Make a donation'],
        ['command' => 'feedback', 'description' => 'Send a feedback about the bot'],
        ['command' => 'privacy', 'description' => 'Privacy Policy'],
        ['command' => 'about', 'description' => 'About the bots'],
    ],

    'donations' => [
        'enabled' => (int) env('BOT_DONATIONS_ENABLED', false),
        'terms' => env('BOT_DONATIONS_TERMS'),
        'providers' => [
            'telegram' => env('BOT_DONATIONS_PROVIDER_TELEGRAM_TOKEN'),
            'github' => env('BOT_DONATIONS_PROVIDER_GITHUB_URL'),
            'paypal' => env('BOT_DONATIONS_PROVIDER_PAYPAL_URL'),
        ],
    ],

];
