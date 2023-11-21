<?php

namespace App\Telegram\Commands;

use App\Telegram\Conversations\SettingsConversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Handlers\Type\Command;

class SettingsCommand extends Command
{
    protected string $command = 'settings';

    protected ?string $description = 'Bot settings';

    public function handle(Nutgram $bot): void
    {
        SettingsConversation::begin($bot);
    }
}
