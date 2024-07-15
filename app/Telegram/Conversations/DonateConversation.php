<?php

namespace App\Telegram\Conversations;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Payment\LabeledPrice;

class DonateConversation extends Conversation
{
    public function start(Nutgram $bot): void
    {
        $bot->sendMessage(
            text: message('donate.main'),
            parse_mode: ParseMode::HTML,
            disable_web_page_preview: true,
        );

        $this->next('getAmount');

        stats('command.donate');
    }

    public function getAmount(Nutgram $bot): void
    {
        //get the amount
        $amount = (int)$bot->message()?->text;

        if ($amount < 1) {
            $bot->sendMessage(
                text: message('donate.invalid'),
                parse_mode: ParseMode::HTML,
            );
            $this->next('getAmount');

            return;
        }

        $this->bot->sendInvoice(
            title: trans('donate.donation'),
            description: trans('donate.support_by_donating'),
            payload: 'donation',
            provider_token: '',
            currency: 'XTR',
            prices: [LabeledPrice::make("$amount XTR", $amount)]
        );

        $this->end();

        stats('donate.invoice', ['value' => $amount]);
    }
}
