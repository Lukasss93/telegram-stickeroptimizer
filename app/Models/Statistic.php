<?php

namespace App\Models;

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
    protected $dates = ['collected_at'];
    protected $casts = ['value' => 'array'];
    protected $guarded = [];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class, 'chat_id', 'chat_id');
    }

    public static function getStatsForBot(): array
    {
        $date = now();

        $stickersNew = self::query()
            ->where('action', 'sticker')
            ->where('category', 'optimized')
            ->whereDate('collected_at', $date->toDateString())
            ->count();

        $stickersTotal = self::query()
            ->where('action', 'sticker')
            ->where('category', 'optimized')
            ->count();

        $usersNewToday = Chat::query()
            ->whereDate('created_at', $date->toDateString())
            ->count();

        $usersActiveToday = self::query()
            ->distinct()
            ->whereDate('collected_at', $date->toDateString())
            ->whereNotNull('chat_id')
            ->count('chat_id');

        $usersTotal = Chat::count();

        return [
            'stickers_new' => number_format($stickersNew, thousands_separator: '˙'),
            'stickers_total' => number_format($stickersTotal, thousands_separator: '˙'),
            'users_new_today' => number_format($usersNewToday, thousands_separator: '˙'),
            'users_active_today' => number_format($usersActiveToday, thousands_separator: '˙'),
            'users_total' => number_format($usersTotal, thousands_separator: '˙'),
            'last_update' => now()->format('Y-m-d H:i:s e'),
        ];
    }
}
