<?php

use App\Telegram\Commands\AboutCommand;
use App\Telegram\Commands\PrivacyCommand;
use App\Telegram\Commands\StartCommand;
use App\Telegram\Handlers\ExceptionsHandler;
use App\Telegram\Handlers\UpdateChatStatus;
use App\Telegram\Middleware\CheckMaintenance;
use App\Telegram\Middleware\CollectChat;
use App\Telegram\Middleware\CheckOnline;
use SergiX44\Nutgram\Nutgram;

/** @var Nutgram $bot */

$bot->middleware(CheckMaintenance::class);
$bot->middleware(CheckOnline::class);
$bot->middleware(CollectChat::class);

$bot->onMyChatMember(UpdateChatStatus::class);

$bot->onCommand('start', StartCommand::class);
$bot->onCommand('help', StartCommand::class);
$bot->onCommand('privacy', PrivacyCommand::class);
$bot->onCommand('about', AboutCommand::class);

$bot->onException(ExceptionsHandler::class);
$bot->onApiError(ExceptionsHandler::class);
