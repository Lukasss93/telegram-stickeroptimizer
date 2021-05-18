<?php

use App\Telegram\Commands\{AboutCommand, PrivacyCommand, StartCommand, StatsCommand};
use App\Telegram\Conversations\{DonateConversation, FeedbackConversation};
use App\Telegram\Handlers\{ExceptionsHandler, PreCheckoutQueryHandler, SuccessfulPaymentHandler, UpdateChatStatus};
use App\Telegram\Middleware\{CheckMaintenance, CheckOnline, CollectChat, DonationsEnabled};
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\MessageTypes;

/** @var Nutgram $bot */

$bot->middleware(CheckMaintenance::class);
$bot->middleware(CheckOnline::class);
$bot->middleware(CollectChat::class);

$bot->onMyChatMember(UpdateChatStatus::class);

$bot->onCommand('start', StartCommand::class);
$bot->onCommand('help', StartCommand::class);
$bot->onCommand('start donate', DonateConversation::class)->middleware(DonationsEnabled::class);
$bot->onCommand('donate', DonateConversation::class)->middleware(DonationsEnabled::class);
$bot->onCommand('stats', StatsCommand::class);
$bot->onCommand('feedback', FeedbackConversation::class);
$bot->onCommand('privacy', PrivacyCommand::class);
$bot->onCommand('about', AboutCommand::class);

$bot->onPreCheckoutQuery(PreCheckoutQueryHandler::class);
$bot->onMessageType(MessageTypes::SUCCESSFUL_PAYMENT, SuccessfulPaymentHandler::class);

$bot->onException(ExceptionsHandler::class);
$bot->onApiError(ExceptionsHandler::class);
