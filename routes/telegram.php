<?php

/** @var Nutgram $bot */

use App\Telegram\Commands;
use App\Telegram\Commands\StatsCommand;
use App\Telegram\Conversations\DonateConversation;
use App\Telegram\Exceptions;
use App\Telegram\Handlers;
use App\Telegram\Middleware;
use SergiX44\Nutgram\Nutgram;

/*
|--------------------------------------------------------------------------
| Global middlewares
|--------------------------------------------------------------------------
*/

$bot->middleware(Middleware\CollectChat::class);
$bot->middleware(Middleware\SetLocale::class);
$bot->middleware(Middleware\CheckMaintenance::class);

/*
|--------------------------------------------------------------------------
| Bot handlers
|--------------------------------------------------------------------------
*/

$bot->onMyChatMember(Handlers\UpdateChatStatusHandler::class);
$bot->onSticker(Handlers\StickerHandler::class);
$bot->onDocument(Handlers\DocumentHandler::class);
$bot->onAnimation(Handlers\AnimationHandler::class);
$bot->onVideo(Handlers\VideoHandler::class);
$bot->onPhoto(Handlers\PhotoHandler::class);
$bot->onPreCheckoutQuery(Handlers\PreCheckoutQueryHandler::class);
$bot->onSuccessfulPayment(Handlers\SuccessfulPaymentHandler::class);
$bot->onCallbackQueryData('stats:{value}', [StatsCommand::class, 'updateStatsMessage']);

/*
|--------------------------------------------------------------------------
| Bot commands
|--------------------------------------------------------------------------
*/

$bot->registerCommand(Commands\StartCommand::class);
$bot->registerCommand(Commands\HelpCommand::class);
$bot->registerCommand(Commands\SettingsCommand::class);

$bot->group(function(Nutgram $bot){
    $bot->onCommand('donate', DonateConversation::class)->description('Make a donation');
    $bot->onCommand('start donate', DonateConversation::class);
})->unless(!config('donation.enabled'));

$bot->registerCommand(Commands\StatsCommand::class);
$bot->registerCommand(Commands\FeedbackCommand::class);
$bot->registerCommand(Commands\AboutCommand::class);
$bot->registerCommand(Commands\PrivacyCommand::class);
$bot->registerCommand(Commands\CancelCommand::class);

/*
|--------------------------------------------------------------------------
| Exception handlers
|--------------------------------------------------------------------------
*/

$bot->registerApiException(Exceptions\UserBlockedException::class);
$bot->registerApiException(Exceptions\UserDeactivatedException::class);
$bot->registerApiException(Exceptions\MessageNotModifiedException::class);
$bot->registerApiException(Exceptions\MessageToEditNotFoundException::class);
$bot->registerApiException(Exceptions\MessageToDeleteNotFoundException::class);
$bot->registerApiException(Exceptions\WrongFileIdException::class);

$bot->onApiError([Handlers\ExceptionsHandler::class, 'api']);
$bot->onException([Handlers\ExceptionsHandler::class, 'global']);
