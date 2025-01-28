<?php

namespace App\Enums;

use App\Models\Chat;
use App\Models\Statistic;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

enum Stats: string
{
    protected const string CACHE_KEY = 'stats';

    case STICKERS_OPTIMIZED = 'stickers_optimized';
    case VIDEOS_OPTIMIZED = 'videos_optimized';
    case ACTIVE_USERS = 'active_users';
    case USERS = 'users';

    public function title(): string
    {
        return match ($this) {
            self::STICKERS_OPTIMIZED => __('stats.category.optimized.stickers'),
            self::VIDEOS_OPTIMIZED => __('stats.category.optimized.videos'),
            self::ACTIVE_USERS => __('stats.category.active_users'),
            self::USERS => __('stats.category.new_users'),
        };
    }

    public function isCached(): bool
    {
        return Cache::has(sprintf('%s.%s', self::CACHE_KEY, $this->value));
    }

    public function clearCache(): void
    {
        Cache::forget(sprintf('%s.%s', self::CACHE_KEY, $this->value));
    }

    public function data(): array
    {
        return Cache::rememberForever(sprintf('%s.%s', self::CACHE_KEY, $this->value), function () {
            return match ($this) {
                self::STICKERS_OPTIMIZED => [
                    'today' => Statistic::optimizedStickers()->ofToday()->count(),
                    'yesterday' => Statistic::optimizedStickers()->ofYesterday()->count(),
                    'last_7_days' => Statistic::optimizedStickers()->ofLast7Days()->count(),
                    'last_30_days' => Statistic::optimizedStickers()->ofLast30Days()->count(),
                    'year' => Statistic::optimizedStickers()->ofThisYear()->count(),
                    'total' => Statistic::optimizedStickers()->count(),
                    'last_update' => now()->format('Y-m-d H:i:s e'),
                ],
                self::VIDEOS_OPTIMIZED => [
                    'today' => Statistic::optimizedVideos()->ofToday()->count(),
                    'yesterday' => Statistic::optimizedVideos()->ofYesterday()->count(),
                    'last_7_days' => Statistic::optimizedVideos()->ofLast7Days()->count(),
                    'last_30_days' => Statistic::optimizedVideos()->ofLast30Days()->count(),
                    'year' => Statistic::optimizedVideos()->ofThisYear()->count(),
                    'total' => Statistic::optimizedVideos()->count(),
                    'last_update' => now()->format('Y-m-d H:i:s e'),
                ],
                self::ACTIVE_USERS => [
                    'today' => Statistic::userActions()->ofToday()->count('chat_id'),
                    'yesterday' => Statistic::userActions()->ofYesterday()->count('chat_id'),
                    'last_7_days' => Statistic::userActions()->ofLast7Days()->count('chat_id'),
                    'last_30_days' => Statistic::userActions()->ofLast30Days()->count('chat_id'),
                    'year' => Statistic::userActions()->ofThisYear()->count('chat_id'),
                    'total' => null,
                    'last_update' => now()->format('Y-m-d H:i:s e'),
                ],
                self::USERS => [
                    'today' => Chat::ofToday()->count(),
                    'yesterday' => Chat::ofYesterday()->count(),
                    'last_7_days' => Chat::ofLast7Days()->count(),
                    'last_30_days' => Chat::ofLast30Days()->count(),
                    'year' => Chat::ofThisYear()->count(),
                    'total' => Chat::count(),
                    'last_update' => now()->format('Y-m-d H:i:s e'),
                ],
            };
        });
    }

    public static function keyboard(): InlineKeyboardMarkup
    {
        $keyboard = InlineKeyboardMarkup::make();

        collect(self::cases())
            ->chunk(2)
            ->each(function (Collection $chunk) use ($keyboard) {
                $buttons = $chunk->map(fn (Stats $stat) => InlineKeyboardButton::make(
                    text: $stat->title(),
                    callback_data: sprintf('stats:%s', $stat->value)
                ));
                $keyboard->addRow(...$buttons);
            });

        return $keyboard;
    }

    public static function cache(): void
    {
        foreach (self::cases() as $stats) {
            $stats->clearCache();
            $stats->data();
        }
    }
}
