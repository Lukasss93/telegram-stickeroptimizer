<?php

namespace App\Telegram\Conversations;

use App\Enums\WatermarkPosition;
use App\Models\Chat;
use Glorand\Model\Settings\Managers\TableSettingsManager;
use Illuminate\Support\Facades\App;
use InvalidArgumentException;
use SergiX44\Nutgram\Conversations\InlineMenu;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use UnexpectedValueException;

class SettingsConversation extends InlineMenu
{
    protected TableSettingsManager $settings;

    public function start(Nutgram $bot): void
    {
        $this->settings = $this->settings ?? $bot->getData(Chat::class)->settings();

        $this
            ->clearButtons()
            ->menuText(message('settings.main', [
                'news' => $this->settings->get('news'),
                'language' => language($this->settings->get('language')),
            ]), [
                'parse_mode' => ParseMode::HTML,
                'disable_web_page_preview' => true,
            ])->addButtonRow(
                InlineKeyboardButton::make(
                    !$this->settings->get('news') ? trans('settings.enable_news') : trans('settings.disable_news'),
                    callback_data: 'settings:news@handleNews'),
            )->addButtonRow(
                InlineKeyboardButton::make(
                    trans('settings.language.title'),
                    callback_data: 'settings:languages@handleLanguages'
                )
            )->addButtonRow(
                InlineKeyboardButton::make(
                    trans('settings.watermark.title'),
                    callback_data: 'settings:watermark@handleWatermark'
                )
            )->addButtonRow(
                InlineKeyboardButton::make(
                    '❌ '.trans('common.close'),
                    callback_data: 'donate.cancel@end')
            )->showMenu();

        stats('settings', 'command');
    }

    protected function handleNews(Nutgram $bot): void
    {
        $this->settings->set('news', !$this->settings->get('news'));

        stats('news action', 'settings', ['status' => $this->settings->get('news')]);

        $this->start($bot);
    }

    protected function handleLanguages(Nutgram $bot): void
    {
        $this
            ->clearButtons()
            ->menuText(message('settings.language', [
                'language' => language($this->settings->get('language')),
                'localization' => config('bot.localization'),
            ]), [
                'parse_mode' => ParseMode::HTML,
                'disable_web_page_preview' => true,
            ]);

        collect(language())
            ->map(fn ($item, $key) => InlineKeyboardButton::make($item, callback_data: "language:$key@setLanguage"))
            ->chunk(2)
            ->each(fn ($row) => $this->addButtonRow(...$row->values()));

        $this->addButtonRow(InlineKeyboardButton::make(trans('settings.back'), callback_data: 'languages:back@start'));

        $this->showMenu();
    }

    protected function setLanguage(Nutgram $bot): void
    {
        [, $language] = explode(':', $bot->callbackQuery()->data);

        $this->settings->set('language', $language);

        App::setLocale($language ?? config('app.locale'));

        $this->handleLanguages($bot);
    }

    protected function handleWatermark(Nutgram $bot, bool $reopen = false): void
    {
        $this
            ->clearButtons()
            ->menuText(message('settings.watermark', [
                'opacity' => $this->settings->get('watermark.opacity'),
                'position' => WatermarkPosition::tryFrom($this->settings->get('watermark.position'))?->emoji(),
                'textContent' => $this->settings->get('watermark.text.content'),
                'textSize' => $this->settings->get('watermark.text.size'),
                'textColor' => $this->settings->get('watermark.text.color'),
                'borderSize' => $this->settings->get('watermark.border.size'),
                'borderColor' => $this->settings->get('watermark.border.color'),
            ]), [
                'parse_mode' => ParseMode::HTML,
                'disable_web_page_preview' => true,
            ]);

        $this->addButtonRow(
            InlineKeyboardButton::make(trans('watermark.opacity.set'), callback_data: 'watermark:opacity:set@setOpacity'),
            InlineKeyboardButton::make(trans('watermark.position.set'), callback_data: 'watermark:position:set@setPosition')
        );
        $this->addButtonRow(
            InlineKeyboardButton::make(trans('watermark.text.content.set'), callback_data: 'watermark:text:content@setTextContent'),
        );
        $this->addButtonRow(
            InlineKeyboardButton::make(trans('watermark.text.size.set'), callback_data: 'watermark:text:size@setTextSize'),
            InlineKeyboardButton::make(trans('watermark.text.color.set'), callback_data: 'watermark:text:color@setTextColor')
        );
        $this->addButtonRow(
            InlineKeyboardButton::make(trans('watermark.border.size.set'), callback_data: 'watermark:border:size@setBorderSize'),
            InlineKeyboardButton::make(trans('watermark.border.color.set'), callback_data: 'watermark:border:color@setBorderColor')
        );
        $this->addButtonRow(
            InlineKeyboardButton::make(trans('settings.back'), callback_data: 'watermark:back@start')
        );

        $this->showMenu($reopen);
    }

    protected function setOpacity(Nutgram $bot): void
    {
        $this
            ->clearButtons()
            ->menuText(trans('watermark.opacity.send'))
            ->orNext('getOpacity');

        $this->showMenu(true);
    }

    protected function getOpacity(Nutgram $bot): void
    {
        try {
            $value = $bot->message()->text;
            if (!is_numeric($value)) {
                throw new UnexpectedValueException('Not a number');
            }

            $value = (int)$value;

            if ($value < 0 || $value > 100) {
                throw new UnexpectedValueException('Invalid range');
            }

            $this->settings->set('watermark.opacity', $value);
            $this->handleWatermark($bot, true);
        } catch (UnexpectedValueException) {
            $bot->sendMessage(trans('common.invalid_value'));
            $this->setOpacity($bot);
        }
    }

    protected function setPosition(Nutgram $bot): void
    {
        $keyboard = ReplyKeyboardMarkup::make();

        foreach (collect(WatermarkPosition::cases())->chunk(3) as $positions) {
            $keyboard->addRow(
                ...$positions->values()->map(fn (WatermarkPosition $value) => KeyboardButton::make($value->emoji())
            )->toArray());
        }

        $this
            ->clearButtons()
            ->menuText(trans('watermark.position.send'), [
                'reply_markup' => $keyboard,
            ])
            ->orNext('getPosition');

        $this->showMenu(true);
    }

    protected function getPosition(Nutgram $bot): void
    {
        try {
            $value = WatermarkPosition::getValueFromEmoji($bot->message()->text);
            $this->settings->set('watermark.position', $value);
            $this->handleWatermark($bot, true);
        } catch (InvalidArgumentException) {
            $bot->sendMessage(trans('common.invalid_value'));
            $this->setPosition($bot);
        }
    }

    protected function setTextContent(Nutgram $bot): void
    {
        $this
            ->clearButtons()
            ->menuText(trans('watermark.text.content.send'))
            ->orNext('getTextContent');

        $this->showMenu(true);
    }

    protected function getTextContent(Nutgram $bot): void
    {
        $value = $bot->message()->text;
        $this->settings->set('watermark.text.content', $value);
        $this->handleWatermark($bot, true);
    }

    protected function setTextSize(Nutgram $bot): void
    {
        $this
            ->clearButtons()
            ->menuText(trans('watermark.text.size.send'))
            ->orNext('getTextSize');

        $this->showMenu(true);
    }

    protected function getTextSize(Nutgram $bot): void
    {
        try {
            $value = $bot->message()->text;
            if (!is_numeric($value)) {
                throw new UnexpectedValueException('Not a number');
            }

            $value = (int)$value;

            if ($value < 1 || $value > 100) {
                throw new UnexpectedValueException('Invalid range');
            }

            $this->settings->set('watermark.text.size', $value);
            $this->handleWatermark($bot, true);
        } catch (UnexpectedValueException) {
            $bot->sendMessage(trans('common.invalid_value'));
            $this->setTextSize($bot);
        }
    }

    protected function setTextColor(Nutgram $bot): void
    {
        $this
            ->clearButtons()
            ->menuText(trans('watermark.text.color.send'))
            ->orNext('getTextColor');

        $this->showMenu(true);
    }

    protected function getTextColor(Nutgram $bot): void
    {
        $value = strtoupper($bot->message()->text);

        if (isHexColor($value)) {
            $this->settings->set('watermark.text.color', $value);
            $this->handleWatermark($bot, true);
        } else {
            $bot->sendMessage(trans('common.invalid_value'));
            $this->setTextColor($bot);
        }
    }

    protected function setBorderSize(Nutgram $bot): void
    {
        $this
            ->clearButtons()
            ->menuText(trans('watermark.border.size.send'))
            ->orNext('getBorderSize');

        $this->showMenu(true);
    }

    protected function getBorderSize(Nutgram $bot): void
    {
        try {
            $value = $bot->message()->text;
            if (!is_numeric($value)) {
                throw new UnexpectedValueException('Not a number');
            }

            $value = (int)$value;

            if ($value < 0 || $value > 10) {
                throw new UnexpectedValueException('Invalid range');
            }

            $this->settings->set('watermark.border.size', $value);
            $this->handleWatermark($bot, true);
        } catch (UnexpectedValueException) {
            $bot->sendMessage(trans('common.invalid_value'));
            $this->setBorderSize($bot);
        }
    }

    protected function setBorderColor(Nutgram $bot): void
    {
        $this
            ->clearButtons()
            ->menuText(trans('watermark.border.color.send'))
            ->orNext('getBorderColor');

        $this->showMenu(true);
    }

    protected function getBorderColor(Nutgram $bot): void
    {
        $value = strtoupper($bot->message()->text);

        if (isHexColor($value)) {
            $this->settings->set('watermark.border.color', $value);
            $this->handleWatermark($bot, true);
        } else {
            $bot->sendMessage(trans('common.invalid_value'));
            $this->setTextColor($bot);
        }
    }


}
