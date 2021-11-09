<?php

namespace App\Telegram\Handlers;

use App\Enums\ExceptionType;
use SergiX44\Nutgram\Nutgram;
use Throwable;

class ExceptionsHandler
{
    public function api(Nutgram $bot, Throwable $e): void
    {
        $this->reportException($bot, $e);
    }

    public function global(Nutgram $bot, Throwable $e): void
    {
        if (ExceptionType::getAllExceptions()->contains(fn ($exception) => $e instanceof $exception)) {
            return;
        }

        $this->reportException($bot, $e);
    }

    public function reportException(Nutgram $bot, Throwable $e): void
    {
        report($e);

        $bot->sendMessage(message('exception', [
            'name' => last(explode('\\', $e::class)),
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => str_replace(base_path(), '', $e->getFile()),
        ]), [
            'chat_id' => config('developer.id'),
        ]);
    }
}
