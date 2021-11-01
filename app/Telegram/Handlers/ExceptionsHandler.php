<?php

namespace App\Telegram\Handlers;

use App\Exceptions\TelegramMessageNotModifiedException;
use App\Exceptions\TelegramUserBlockedException;
use App\Exceptions\TelegramUserDeactivatedException;
use SergiX44\Nutgram\Nutgram;
use Throwable;

class ExceptionsHandler
{
    protected array $skipExceptions = [
        'bot was blocked by the user' => TelegramUserBlockedException::class,
        'user is deactivated' => TelegramUserDeactivatedException::class,
        'specified new message content and reply markup are exactly the same' => TelegramMessageNotModifiedException::class,
    ];

    public function api(Nutgram $bot, Throwable $e): void
    {
        foreach ($this->skipExceptions as $message => $exception) {
            if (str_contains($e->getMessage(), $message)) {
                throw new $exception($e->getMessage());
            }
        }

        $this->reportException($bot, $e);
    }


    public function global(Nutgram $bot, Throwable $e): void
    {
        foreach ($this->skipExceptions as $exception) {
            if ($e instanceof $exception) {
                return;
            }
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
