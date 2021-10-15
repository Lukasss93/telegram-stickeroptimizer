<?php

return [

    'online' => (bool) env('BOT_ONLINE', true),
    'name' => env('BOT_NAME', env('APP_NAME', 'Telegram Bot')),
    'username' => env('BOT_USERNAME'),
    'version' => env('BOT_VERSION'),
    'source' => env('BOT_SOURCE'),
    'changelog' => env('BOT_CHANGELOG'),
    'privacy' => env('BOT_PRIVACY'),

    'donations' => [
        'enabled' => (int)env('BOT_DONATIONS_ENABLED', false),
        'terms' => env('BOT_DONATIONS_TERMS'),
        'provider_token' => env('BOT_DONATIONS_PROVIDER_TOKEN'),
        'third_party_providers' => [
            'url' => [
                'Github Sponsor' => env('BOT_DONATIONS_THIRD_PARTY_PROVIDERS_URL_GITHUB'),
                'PayPal' => env('BOT_DONATIONS_THIRD_PARTY_PROVIDERS_URL_PAYPAL'),
            ],
            'text' => [
                'BTC' => env('BOT_DONATIONS_THIRD_PARTY_PROVIDERS_TEXT_BTC'),
                'ETH' => env('BOT_DONATIONS_THIRD_PARTY_PROVIDERS_TEXT_ETH'),
                'DOGE' => env('BOT_DONATIONS_THIRD_PARTY_PROVIDERS_TEXT_DOGE'),
            ],
        ],
        'message_in_caption' => (bool)env('BOT_DONATIONS_MESSAGE_IN_CAPTION', false),
    ],

    'news' => [
        'channel_id' => (int) env('BOT_NEWS_CHANNEL_ID'),
        'rate' => (int) env('BOT_NEWS_RATE', 2000),
        'queue' => 'news',
    ],

];
