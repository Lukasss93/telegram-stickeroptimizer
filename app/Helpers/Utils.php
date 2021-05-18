<?php

use App\Models\Statistic;
use SergiX44\Nutgram\Nutgram;

/**
 * Render an HTML message
 * @param string $view
 * @param array $values
 * @return string
 */
function message(string $view, array $values = []): string
{
    return rescue(function () use ($view, $values) {
        return (string) Str::of(view("messages.$view", $values)->render())
            //remove line breaks
            ->replaceMatches('/\r\n|\r|\n/', '')
            //replace <br> with \n
            ->replace(['<br>', '<BR>'], "\n");
    }, 'messages.'.$view, true);
}

/**
 * Convert string to boolean
 * @param $value
 * @return bool
 */
function stringToBoolean($value): bool
{
    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
}

/**
 * Returns a list of locales or a locale by code
 * @param string|null $code
 * @return array|string|null
 */
function locale(string $code = null): array|string|null
{
    $locales = config('locales');

    return $code === null ? $locales : ($locales[$code] ?? null);
}

function stats(string $action, string $category = null, array $value = null, int $chat_id = null): void
{
    Statistic::create([
        'action' => $action,
        'category' => $category,
        'value' => $value,
        'chat_id' => $chat_id ?? app(Nutgram::class)->update()?->getChat()?->id,
    ]);
}
