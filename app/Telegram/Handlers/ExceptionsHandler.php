<?php


namespace App\Telegram\Handlers;


use SergiX44\Nutgram\Nutgram;
use Throwable;

class ExceptionsHandler
{
    public function __invoke(Nutgram $bot, Throwable $e)
    {
        report($e);

        $bot->sendMessage(message('exception', [
            'name' => last(explode('\\', $e::class)),
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => str_replace(base_path(), '', $e->getFile()),
        ]), [
            'chat_id' => config('telegram.developer.id'),
        ]);
    }
}
