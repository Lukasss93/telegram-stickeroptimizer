<?php

return [
    'enabled' => (int)env('DONATION_ENABLED', false),
    'terms' => env('DONATION_TERMS'),
    'provider_token' => env('DONATION_PROVIDER_TOKEN'),
    'third_party_providers' => [
        'url' => [
            'Github Sponsor' => env('DONATION_THIRD_PARTY_PROVIDERS_URL_GITHUB'),
            'PayPal' => env('DONATION_THIRD_PARTY_PROVIDERS_URL_PAYPAL'),
        ],
        'text' => [
            'BTC' => env('DONATION_THIRD_PARTY_PROVIDERS_TEXT_BTC'),
            'ETH' => env('DONATION_THIRD_PARTY_PROVIDERS_TEXT_ETH'),
            'DOGE' => env('DONATION_THIRD_PARTY_PROVIDERS_TEXT_DOGE'),
        ],
    ],
    'message_in_caption' => (bool)env('DONATION_MESSAGE_IN_CAPTION', false),
];
