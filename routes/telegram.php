<?php

/** @var Nutgram $bot */

use App\Telegram\Commands\AboutCommand;
use App\Telegram\Commands\CancelCommand;
use App\Telegram\Commands\HelpCommand;
use App\Telegram\Commands\PrivacyCommand;
use App\Telegram\Commands\StartCommand;
use App\Telegram\Commands\StatsCommand;
use App\Telegram\Conversations\DonateConversation;
use App\Telegram\Conversations\FeedbackConversation;
use App\Telegram\Conversations\SettingsConversation;
use App\Telegram\Handlers\DocumentHandler;
use App\Telegram\Handlers\ExceptionsHandler;
use App\Telegram\Handlers\PhotoHandler;
use App\Telegram\Handlers\PreCheckoutQueryHandler;
use App\Telegram\Handlers\StickerHandler;
use App\Telegram\Handlers\SuccessfulPaymentHandler;
use App\Telegram\Handlers\UpdateChatStatusHandler;
use App\Telegram\Middleware\CheckMaintenance;
use App\Telegram\Middleware\CheckOffline;
use App\Telegram\Middleware\CheckRateLimit;
use App\Telegram\Middleware\CollectChat;
use App\Telegram\Middleware\SetLocale;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\MessageTypes;

/*
|--------------------------------------------------------------------------
| Global middlewares
|--------------------------------------------------------------------------
*/

$bot->middleware(CollectChat::class);
$bot->middleware(SetLocale::class);
$bot->middleware(CheckRateLimit::class);
$bot->middleware(CheckMaintenance::class);
$bot->middleware(CheckOffline::class);

/*
|--------------------------------------------------------------------------
| Bot handlers
|--------------------------------------------------------------------------
*/

$bot->onMyChatMember(UpdateChatStatusHandler::class);

$bot->onMessageType(MessageTypes::STICKER, StickerHandler::class);
$bot->onMessageType(MessageTypes::DOCUMENT, DocumentHandler::class);
$bot->onMessageType(MessageTypes::PHOTO, PhotoHandler::class);

$bot->onPreCheckoutQuery(PreCheckoutQueryHandler::class);
$bot->onMessageType(MessageTypes::SUCCESSFUL_PAYMENT, SuccessfulPaymentHandler::class);

/*
|--------------------------------------------------------------------------
| Bot commands
|--------------------------------------------------------------------------
*/

$bot->onCommand('start', StartCommand::class)->description('Welcome message');
$bot->onCommand('help', HelpCommand::class)->description('Help message');
$bot->onCommand('about', AboutCommand::class)->description('About the bots');
$bot->onCommand('privacy', PrivacyCommand::class)->description('Privacy Policy');

if (config('donation.enabled')) {
    $bot->onCommand('donate', DonateConversation::class)->description('Make a donation');
    $bot->onCommand('start donate', DonateConversation::class);
}

$bot->onCommand('stats', StatsCommand::class)->description('Show bot statistics');
$bot->onCommand('feedback', FeedbackConversation::class)->description('Send a feedback about the bot');
$bot->onCommand('settings', SettingsConversation::class)->description('Bot Settings');
$bot->onCommand('cancel', CancelCommand::class)->description('Close a conversation or a keyboard');

/*
|--------------------------------------------------------------------------
| Exception handlers
|--------------------------------------------------------------------------
*/

$bot->onException(ExceptionsHandler::class);
$bot->onApiError(ExceptionsHandler::class);
