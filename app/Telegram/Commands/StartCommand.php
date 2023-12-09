<?php

namespace App\Telegram\Commands;

use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

class StartCommand extends Command
{
    protected string $command = 'start';

    protected ?string $description = 'Welcome message';

    public function handle(Nutgram $bot): void
    {
        $bot->sendMessage(
            text: message('start'),
            parse_mode: ParseMode::HTML,
        );

        stats('command.start');
    }
}
