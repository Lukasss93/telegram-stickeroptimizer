<?php

/** @var Nutgram $bot */

use App\Telegram\Commands\{AboutCommand, HelpCommand, PrivacyCommand, StartCommand, StatsCommand};
use App\Telegram\Conversations\{DonateConversation, FeedbackConversation};
use App\Telegram\Handlers\{ExceptionsHandler,
    PreCheckoutQueryHandler,
    SuccessfulPaymentHandler,
    UpdateChatStatusHandler
};
use App\Telegram\Middleware\{CheckMaintenance, CheckOffline, CheckRateLimit, CollectChat, SetLocale};
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\MessageTypes;

$bot->middleware(CollectChat::class);
$bot->middleware(SetLocale::class);
$bot->middleware(CheckRateLimit::class);
$bot->middleware(CheckMaintenance::class);
$bot->middleware(CheckOffline::class);

$bot->onMyChatMember(UpdateChatStatusHandler::class);

$bot->onCommand('start', StartCommand::class)->description('Welcome message');
$bot->onCommand('help', HelpCommand::class)->description('Help message');
$bot->onCommand('about', AboutCommand::class)->description('About the bots');
$bot->onCommand('privacy', PrivacyCommand::class)->description('Privacy Policy');
$bot->onCommand('stats', StatsCommand::class)->description('Show bot statistics');
$bot->onCommand('feedback', FeedbackConversation::class)->description('Send a feedback about the bot');
$bot->onCommand('cancel', fn (Nutgram $bot) => $bot->endConversation());

if (config('donation.enabled')) {
    $bot->onCommand('donate', DonateConversation::class)->description('Make a donation');
    $bot->onCommand('start donate', DonateConversation::class);
}

$bot->onPreCheckoutQuery(PreCheckoutQueryHandler::class);
$bot->onMessageType(MessageTypes::SUCCESSFUL_PAYMENT, SuccessfulPaymentHandler::class);

$bot->onException(ExceptionsHandler::class);
$bot->onApiError(ExceptionsHandler::class);
