<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static Builder ofThisYear()
 * @mixin Model
 */
trait ExtraDateScopes
{
    public function scopeOfThisYear(Builder $query): Builder
    {
        $createdColumnName = self::CREATED_AT !== 'created_at' ? self::CREATED_AT : config('date-scopes.created_column');
        $now = CarbonImmutable::now();

        return $query->whereBetween($createdColumnName, [$now->startOfYear(), $now->endOfYear()]);
    }
}
