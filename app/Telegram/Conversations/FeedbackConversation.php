<?php


namespace App\Telegram\Conversations;


use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Types\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\InlineKeyboardMarkup;

class FeedbackConversation extends Conversation
{
    protected ?string $step = 'askForFeedback';
    protected ?string $feedback;

    /**
     * Ask for feedback text
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function askForFeedback(Nutgram $bot): void
    {
        $bot->sendMessage(message('feedback.ask'), [
            'reply_markup' => InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make(trans('common.cancel'), callback_data: 'feedback.cancel')
                ),
        ]);
        $this->setSkipHandlers(true)->next('getFeedback');
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
            $callbackMessage = $bot->callbackQuery()->message;
            $bot->answerCallbackQuery();
            $bot->deleteMessage($callbackMessage->chat->id, $callbackMessage->message_id);
            $bot->sendMessage(message('feedback.cancel'));
            $this->end();
            return;
        }

        //get the message
        $message = $bot->message();

        //check valid input
        if ($message === null || $message->text === null) {
            $bot->sendMessage(message('feedback.wrong'), [
                'parse_mode' => ParseMode::HTML,
            ]);
            $this->askForFeedback($bot);
            return;
        }

        //get the input
        $this->feedback = $message->text;

        //thank the user
        $bot->sendMessage(message('feedback.thanks'));

        //send feedback to dev
        $bot->sendMessage(message('feedback.received', [
            'from' => "{$message->from->first_name} {$message->from->last_name}",
            'username' => $message->from->username,
            'user_id' => $message->from->id,
            'message' => $this->feedback,
        ]), [
            'chat_id' => config('developer.id'),
        ]);

        //close conversation
        $this->end();
    }
}
