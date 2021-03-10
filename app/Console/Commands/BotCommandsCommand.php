<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\BotCommand;

class BotCommandsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:commands';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build the bot commands';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->warn('Building commands...');

        /** @var Nutgram $bot */
        $bot = app(Nutgram::class);

        $commands = collect([])
            ->put('start', 'Welcome message')
            ->put('help', 'Help message')
            ->put('about', 'About the bot')
            ->map(function ($item, $key) {
                $command = new BotCommand();
                $command->command = $key;
                $command->description = $item;
                return $command;
            })
            ->values()
            ->toArray();

        $bot->setMyCommands($commands);

        $this->info('Done.');

        return 0;
    }
}
