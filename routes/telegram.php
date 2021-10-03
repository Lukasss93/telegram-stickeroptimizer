<?php

/** @var Nutgram $bot */

use App\Telegram\Commands\{AboutCommand, HelpCommand, PrivacyCommand, StartCommand, StatsCommand};
use App\Telegram\Conversations\{DonateConversation, FeedbackConversation};
use App\Telegram\Handlers\{ExceptionsHandler, PreCheckoutQueryHandler, SuccessfulPaymentHandler, UpdateChatStatus};
use App\Telegram\Middleware\{CheckMaintenance, CheckOnline, CollectChat, DonationsEnabled};
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\MessageTypes;

$bot->middleware(CheckMaintenance::class);
$bot->middleware(CheckOnline::class);
$bot->middleware(CollectChat::class);

$bot->onMyChatMember(UpdateChatStatus::class);

$bot->onCommand('start', StartCommand::class)->description('Welcome message');
$bot->onCommand('help', HelpCommand::class)->description('Help message');
$bot->onCommand('stats', StatsCommand::class)->description('Show bot statistics');
$bot->onCommand('feedback', FeedbackConversation::class)->description('Send a feedback about the bot');
$bot->onCommand('privacy', PrivacyCommand::class)->description('Privacy Policy');
$bot->onCommand('about', AboutCommand::class)->description('About the bots');

if(config('bot.donations.enabled')){
    $bot->onCommand('donate', DonateConversation::class)->middleware(DonationsEnabled::class)->description('Make a donation');
    $bot->onCommand('start donate', DonateConversation::class)->middleware(DonationsEnabled::class);
}

$bot->onPreCheckoutQuery(PreCheckoutQueryHandler::class);
$bot->onMessageType(MessageTypes::SUCCESSFUL_PAYMENT, SuccessfulPaymentHandler::class);

$bot->onException(ExceptionsHandler::class);
$bot->onApiError(ExceptionsHandler::class);
