<?php

return [
    // The Telegram BOT api token
    'token' => env('TELEGRAM_TOKEN', ''),

    // Extra or specific configurations
    'config' => [
        'read_timeout' => 20,
        'timeout' => 20,
    ],

    // Set if the service provider should automatically load
    // handlers from /routes/telegram.php
    'routes' => true,
];
