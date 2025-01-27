<?php

namespace App\Telegram\Commands;

use App\Enums\Stats;
use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

class StatsCommand extends Command
{
    protected string $command = 'stats';

    protected ?string $description = 'Show bot statistics';

    public function handle(Nutgram $bot): void
    {
        [$message, $keyboard] = $this->renderMessage('stickers_optimized');

        $bot->sendMessage(
            text: $message,
            parse_mode: ParseMode::HTML,
            reply_markup: $keyboard,
        );

        stats('command.stats');
    }

    public function updateStatsMessage(Nutgram $bot, string $value): void
    {
        [$message, $keyboard] = $this->renderMessage($value);

        $bot->editMessageText(
            text: $message,
            parse_mode: ParseMode::HTML,
            reply_markup: $keyboard,
        );

        $bot->answerCallbackQuery();
    }

    protected function renderMessage(string $value): array
    {
        $stats = Stats::from($value);

        if (!$stats->isCached()) {
            return [
                message('stats.empty', ['title' => $stats->title(),]),
                Stats::keyboard(),
            ];
        }

        return [
            message('stats.template', [
                'title' => $stats->title(),
                ...$stats->data(),
            ]),
            Stats::keyboard(),
        ];
    }
}
