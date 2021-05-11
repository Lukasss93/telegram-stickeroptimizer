<?php

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
