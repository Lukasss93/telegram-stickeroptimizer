<?php

use App\Telegram\Commands\StartCommand;
use SergiX44\Nutgram\Nutgram;

/** @var Nutgram $bot */

$bot->onCommand('start', StartCommand::class);
$bot->onCommand('help', StartCommand::class);
