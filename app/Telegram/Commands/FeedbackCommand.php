<?php

namespace App\Telegram\Commands;

use App\Telegram\Conversations\FeedbackConversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Handlers\Type\Command;

class FeedbackCommand extends Command
{
    protected string $command = 'feedback';

    protected ?string $description = 'Send a feedback about the bot';

    public function handle(Nutgram $bot): void
    {
        FeedbackConversation::begin($bot);
    }
}
