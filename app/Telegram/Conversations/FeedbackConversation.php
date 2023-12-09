<?php

namespace App\Telegram\Conversations;

use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class FeedbackConversation extends Conversation
{
    protected ?string $feedback;
    protected bool $success = false;
    protected int $chat_id;
    protected int $message_id;

    /**
     * Ask for feedback text
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function start(Nutgram $bot): void
    {
        $message = $bot->sendMessage(
            text: message('feedback.ask'),
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make(trans('common.cancel'), callback_data: 'feedback.cancel')),
        );

        $this->chat_id = $message->chat->id;
        $this->message_id = $message->message_id;

        $this->next('getFeedback');

        stats('command.feedback');
    }

    /**
     * Get the feedback message
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function getFeedback(Nutgram $bot): void
    {
        //handle cancel button
        if ($bot->isCallbackQuery() && $bot->callbackQuery()->data === 'feedback.cancel') {
            $bot->answerCallbackQuery();
            $this->end();
            stats('feedback.cancelled');

            return;
        }

        //check valid input
        if ($bot->message()?->text === null) {
            $bot->sendMessage(
                text: message('feedback.wrong'),
                parse_mode: ParseMode::HTML,
            );
            $this->start($bot);

            return;
        }

        //get the input
        $this->feedback = $bot->message()?->text;

        //send feedback to dev
        $bot->sendMessage(
            text: message('feedback.received', [
                'from' => "{$bot->user()?->first_name} {$bot->user()?->last_name}",
                'username' => $bot->user()?->username,
                'user_id' => $bot->userId(),
                'message' => $this->feedback,
            ]),
            chat_id: config('developer.id'),
        );

        $this->success = true;

        //close conversation
        $this->end();

        stats('feedback.sent');
    }

    public function closing(Nutgram $bot): void
    {
        $bot->deleteMessage($this->chat_id, $this->message_id);

        if ($this->success) {
            $bot->sendMessage(message('feedback.thanks'));

            return;
        }

        $bot->sendMessage(message('feedback.cancelled'));
    }
}
