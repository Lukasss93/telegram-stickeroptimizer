<?php

use App\Telegram\Commands\AboutCommand;
use App\Telegram\Commands\StartCommand;
use App\Telegram\Handlers\ExceptionsHandler;
use App\Telegram\Middleware\CheckForMaintenance;
use App\Telegram\Middleware\CollectChat;
use App\Telegram\Middleware\CheckOnline;
use SergiX44\Nutgram\Nutgram;

/** @var Nutgram $bot */

$bot->middleware(CheckForMaintenance::class);
$bot->middleware(CheckOnline::class);
$bot->middleware(CollectChat::class);

$bot->onCommand('start', StartCommand::class);
$bot->onCommand('help', StartCommand::class);
$bot->onCommand('about', AboutCommand::class);

$bot->onException(ExceptionsHandler::class);
$bot->onApiError(ExceptionsHandler::class);
