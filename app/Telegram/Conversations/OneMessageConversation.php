<?php

namespace App\Telegram\Conversations;

use SergiX44\Nutgram\Conversation;
use SergiX44\Nutgram\Telegram\Types\Message;

abstract class OneMessageConversation extends Conversation
{
    protected ?int $messageId = null;

    protected ?int $chatId = null;

    protected function updateLastMessageStatus(string $step, ?Message $message = null, bool $noHanlders = false,
                                               bool $noMiddlewares = false): void
    {
        if ($message !== null) {
            $this->messageId = $message->message_id;
            $this->chatId = $message->chat?->id;
        }

        $this->setSkipHandlers($noHanlders)
            ->setSkipMiddlewares($noMiddlewares)
            ->next($step);
    }

    protected function sendOrEditMessage($text, $opt = [], $forceSend = false): ?Message
    {
        if ($this->bot->isCallbackQuery()) {
            $this->bot->answerCallbackQuery();
        }

        if (($this->messageId === null || $this->chatId === null) && !$forceSend) {
            return $this->bot->sendMessage($text, $opt);
        }

        if ($forceSend) {
            $this->disableLastKeyboard();

            return $this->bot->sendMessage($text, $opt);
        }

        $this->bot->editMessageText($text, array_merge([
            'chat_id' => $this->chatId,
            'message_id' => $this->messageId,
        ], $opt));

        return null;
    }

    protected function disableLastKeyboard(): void
    {
        if ($this->messageId !== null && $this->chatId !== null) {
            $this->bot->editMessageReplyMarkup([
                'chat_id' => $this->chatId,
                'message_id' => $this->messageId,
                'reply_markup' => json_encode(['inline_keyboard' => []]),
            ]);
        }
    }

}
