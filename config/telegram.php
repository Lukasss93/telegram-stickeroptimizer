<?php

return [

    'bot' => [
        //bot name
        'name' => env('BOT_NAME', env('APP_NAME', 'Telegram Bot')),

        //bot username
        'username' => env('BOT_USERNAME'),

        //bot version
        'version' => env('BOT_VERSION'),

        //bot source code url
        'source' => env('BOT_SOURCE'),

        //bot changelog url
        'changelog' => env('BOT_CHANGELOG'),
    ],

    'developer' => [
        //developer user id
        'id' => env('DEV_ID'),

        //developer fullname
        'name' => env('DEV_NAME'),

        //developer username
        'username' => env('DEV_USERNAME'),

        //developer email
        'email' => env('DEV_EMAIL'),

        //developer website url
        'website' => env('DEV_WEBSITE'),
    ],

];
