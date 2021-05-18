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
        'start' => 'Welcome message',
        'help' => 'Help message',
        'donate' => 'Make a donation',
        'stats' => 'Show bot statistics',
        'feedback' => 'Send a feedback about the bot',
        'privacy' => 'Privacy Policy',
        'about' => 'About the bots',
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

    'news' => [
        'channel_id' => (int) env('BOT_NEWS_CHANNEL_ID'),
        'rate' => (int) env('BOT_NEWS_RATE', 2000),
        'queue' => 'news',
    ],

];
