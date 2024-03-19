<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Statistic
 *
 * @property int $id
 * @property int|null $chat_id
 * @property string $action
 * @property array|null $value
 * @property string|null $category
 * @property Carbon $collected_at
 * @property-read Chat|null $chat
 * @method static Builder|Statistic newModelQuery()
 * @method static Builder|Statistic newQuery()
 * @method static Builder|Statistic query()
 * @method static Builder|Statistic whereAction($value)
 * @method static Builder|Statistic whereCategory($value)
 * @method static Builder|Statistic whereChatId($value)
 * @method static Builder|Statistic whereCollectedAt($value)
 * @method static Builder|Statistic whereId($value)
 * @method static Builder|Statistic whereValue($value)
 * @mixin Eloquent
 */
class Statistic extends Model
{
    public $timestamps = false;
    protected static $unguarded = true;

    protected function casts(): array
    {
        return [
            'value' => 'array',
            'collected_at' => 'datetime',
        ];
    }

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class, 'chat_id', 'chat_id');
    }

    public static function getStatsForBot(): array
    {
        $date = CarbonImmutable::now();

        // stickers optimized
        $stickersOptimizedYesterday = self::query()
            ->where('action', 'sticker.optimized')
            ->whereBetween('collected_at', [$date->subDay()->startOfDay(), $date->subDay()->endOfDay()])
            ->count();
        $stickersOptimizedToday = self::query()
            ->where('action', 'sticker.optimized')
            ->whereBetween('collected_at', [$date->startOfDay(), $date->endOfDay()])
            ->count();
        $stickersOptimizedWeek = self::query()
            ->where('action', 'sticker.optimized')
            ->whereBetween('collected_at', [$date->startOfWeek(), $date->endOfWeek()])
            ->count();
        $stickersOptimizedMonth = self::query()
            ->where('action', 'sticker.optimized')
            ->whereBetween('collected_at', [$date->startOfMonth(), $date->endOfMonth()])
            ->count();
        $stickersOptimizedYear = self::query()
            ->where('action', 'sticker.optimized')
            ->whereBetween('collected_at', [$date->startOfYear(), $date->endOfYear()])
            ->count();
        $stickersOptimizedTotal = self::query()
            ->where('action', 'sticker.optimized')
            ->count();

        //active users
        $activeUsersYesterday = self::query()
            ->distinct()
            ->whereBetween('collected_at', [$date->subDay()->startOfDay(), $date->subDay()->endOfDay()])
            ->whereNotNull('chat_id')
            ->count('chat_id');
        $activeUsersToday = self::query()
            ->distinct()
            ->whereBetween('collected_at', [$date->startOfDay(), $date->endOfDay()])
            ->whereNotNull('chat_id')
            ->count('chat_id');
        $activeUsersWeek = self::query()
            ->distinct()
            ->whereBetween('collected_at', [$date->startOfWeek(), $date->endOfWeek()])
            ->whereNotNull('chat_id')
            ->count('chat_id');
        $activeUsersMonth = self::query()
            ->distinct()
            ->whereBetween('collected_at', [$date->startOfMonth(), $date->endOfMonth()])
            ->whereNotNull('chat_id')
            ->count('chat_id');
        $activeUsersYear = self::query()
            ->distinct()
            ->whereBetween('collected_at', [$date->startOfYear(), $date->endOfYear()])
            ->whereNotNull('chat_id')
            ->count('chat_id');

        // users
        $usersYesterday = Chat::query()
            ->whereBetween('created_at', [$date->subDay()->startOfDay(), $date->subDay()->endOfDay()])
            ->count();
        $usersToday = Chat::query()
            ->whereBetween('created_at', [$date->startOfDay(), $date->endOfDay()])
            ->count();
        $usersWeek = Chat::query()
            ->whereBetween('created_at', [$date->startOfWeek(), $date->endOfWeek()])
            ->count();
        $usersMonth = Chat::query()
            ->whereBetween('created_at', [$date->startOfMonth(), $date->endOfMonth()])
            ->count();
        $usersYear = Chat::query()
            ->whereBetween('created_at', [$date->startOfYear(), $date->endOfYear()])
            ->count();
        $usersTotal = Chat::count();

        return [
            'stickers_optimized' => [
                'yesterday' => number_format($stickersOptimizedYesterday, thousands_separator: '˙'),
                'today' => number_format($stickersOptimizedToday, thousands_separator: '˙'),
                'week' => number_format($stickersOptimizedWeek, thousands_separator: '˙'),
                'month' => number_format($stickersOptimizedMonth, thousands_separator: '˙'),
                'year' => number_format($stickersOptimizedYear, thousands_separator: '˙'),
                'total' => number_format($stickersOptimizedTotal, thousands_separator: '˙'),
            ],
            'active_users' => [
                'yesterday' => number_format($activeUsersYesterday, thousands_separator: '˙'),
                'today' => number_format($activeUsersToday, thousands_separator: '˙'),
                'week' => number_format($activeUsersWeek, thousands_separator: '˙'),
                'month' => number_format($activeUsersMonth, thousands_separator: '˙'),
                'year' => number_format($activeUsersYear, thousands_separator: '˙'),
            ],
            'users' => [
                'yesterday' => number_format($usersYesterday, thousands_separator: '˙'),
                'today' => number_format($usersToday, thousands_separator: '˙'),
                'week' => number_format($usersWeek, thousands_separator: '˙'),
                'month' => number_format($usersMonth, thousands_separator: '˙'),
                'year' => number_format($usersYear, thousands_separator: '˙'),
                'total' => number_format($usersTotal, thousands_separator: '˙'),
            ],
            'last_update' => $date->format('Y-m-d H:i:s e'),
        ];
    }
}
