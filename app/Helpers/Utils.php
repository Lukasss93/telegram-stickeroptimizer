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
        $message = view("messages.$view", $values)->render();

        //remove line breaks
        $message = preg_replace('/\r\n|\r|\n/', '', $message);

        //replace <br> with \n
        $message = str_replace(['<br>', '<BR>'], "\n", $message);

        return $message;
    }, 'messages.' . $view, true);
}
