<?php

return [
    'enabled' => (int)env('DONATION_ENABLED', false),
    'terms' => env('DONATION_TERMS'),
    'message_in_caption' => (bool)env('DONATION_MESSAGE_IN_CAPTION', false),
];
