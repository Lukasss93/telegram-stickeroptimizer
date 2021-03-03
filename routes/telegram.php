<?php

use SergiX44\Nutgram\Nutgram;

/** @var Nutgram $bot */

$bot->onCommand('start', function(Nutgram $bot){
    $bot->sendMessage('hello world!');
});
