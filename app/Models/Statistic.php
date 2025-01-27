<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LaracraftTech\LaravelDateScopes\DateScopes;

class Statistic extends Model
{
    use DateScopes;

    public $timestamps = false;
    protected static $unguarded = true;
    public const CREATED_AT = 'collected_at';

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

    public function scopeOptimizedStickers(Builder $query): Builder
    {
        return $query->where('action', 'sticker.optimized');
    }

    public function scopeOptimizedVideos(Builder $query): Builder
    {
        return $query->where('action', 'video.optimized');
    }

    public function scopeUserActions(Builder $query): Builder
    {
        return $query->distinct()->whereNotNull('chat_id');
    }
}
