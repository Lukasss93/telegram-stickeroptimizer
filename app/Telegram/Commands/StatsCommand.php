<?php

namespace App\Telegram\Commands;

use Illuminate\Support\Facades\Cache;
use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class StatsCommand extends Command
{
    protected string $command = 'stats';

    protected ?string $description = 'Show bot statistics';

    public function handle(Nutgram $bot): void
    {
        $data = Cache::get('stats');

        if ($data === null) {
            $bot->sendMessage(
                text: message('stats.empty'),
                parse_mode: ParseMode::HTML,
            );

            stats('stats', 'command');

            return;
        }

        $bot->sendMessage(
            text: $this->getMessage($data, 'stickers_optimized'),
            parse_mode: ParseMode::HTML,
            reply_markup: $this->getKeyboard(),
        );

        stats('stats', 'command');
    }

    public function updateStatsMessage(Nutgram $bot, string $value)
    {
        $data = Cache::get('stats');

        if ($data === null) {
            $bot->editMessageText(
                text: message('stats.empty'),
                parse_mode: ParseMode::HTML,
            );

            return;
        }

        $bot->editMessageText(
            text: $this->getMessage($data, $value),
            parse_mode: ParseMode::HTML,
            reply_markup: $this->getKeyboard(),
        );

        $bot->answerCallbackQuery();
    }

    protected function getMessage(array $data, string $value): string
    {
        $title = match ($value) {
            'stickers_optimized' => __('stats.category.optimized'),
            'active_users' => __('stats.category.active_users'),
            'users' => __('stats.category.new_users'),
        };

        return message('stats.template', [
            'title' => $title,
            ...$data[$value],
            'lastUpdate' => $data['last_update'],
        ]);
    }

    protected function getKeyboard(): InlineKeyboardMarkup
    {
        return InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make(__('stats.category.optimized'), callback_data: 'stats:stickers_optimized'),
            )->addRow(
                InlineKeyboardButton::make(__('stats.category.active_users'), callback_data: 'stats:active_users'),
                InlineKeyboardButton::make(__('stats.category.new_users'), callback_data: 'stats:users'),
            );
    }
}
