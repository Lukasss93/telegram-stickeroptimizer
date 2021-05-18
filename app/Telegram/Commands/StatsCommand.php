<?php

namespace App\Telegram\Commands;

use App\Models\Statistic;
use Illuminate\Support\Facades\Cache;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;

class StatsCommand
{
    public function __invoke(Nutgram $bot): void
    {
        $data = Cache::sear('stats', function () {
            return Statistic::getStatsForBot();
        });

        $bot->sendMessage(message('stats', $data), [
            'parse_mode' => ParseMode::HTML,
            'disable_web_page_preview' => true,
        ]);

        stats('stats', 'command');
    }
}
