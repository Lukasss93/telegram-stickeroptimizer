<?php

namespace App\Console\Commands;

use App\Jobs\SendNews;
use App\Models\Chat;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class BotNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:news {id} {--confirm}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send bot news to users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $message_id = (int)$this->argument('id');
        $confirm = $this->option('confirm');

        $this->warn('Dispatching news...');

        //get chats (started, not blocked)
        $chats = Chat::query()
            ->select('chat_id')
            ->when($confirm,
                fn (Builder $query) => $query
                    ->whereNotNull('started_at')
                    ->whereNull('blocked_at')
                    ->whereSettings('news', '=', true, true),
                fn (Builder $query) => $query->where('chat_id', config('developer.id')))
            ->cursor();

        $bar = $this->output->createProgressBar($chats->count());

        $bar->start();

        foreach ($chats as $chat) {
            /** @var Chat $chat */
            SendNews::dispatch($chat->chat_id, $message_id);
            $bar->advance();
        }

        $bar->finish();

        $this->info(PHP_EOL.'Done.');

        return 0;
    }
}
