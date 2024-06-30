<?php

namespace App\Telegram\Conversations;

use App\Enums\StickerTemplate;
use App\Enums\WatermarkPosition;
use App\Models\Chat;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Lukasss93\ModelSettings\Managers\TableSettingsManager;
use SergiX44\Nutgram\Conversations\InlineMenu;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use UnexpectedValueException;

class SettingsConversation extends InlineMenu
{
    protected TableSettingsManager $settings;
    protected bool $reopen = false;

    public function start(Nutgram $bot): void
    {
        $this->settings = $this->settings ?? $bot->get(Chat::class)->settings();

        $this
            ->clearButtons()
            ->menuText(message('settings.main', [
                'news' => $this->settings->get('news'),
                'language' => language($this->settings->get('language')),
                'watermark' => $this->settings->get('watermark.opacity') > 0,
                'template' => StickerTemplate::from($this->settings->get('template'))->getLabel(),
                'trim' => $this->settings->get('trim'),
            ]), [
                'parse_mode' => ParseMode::HTML,
                'disable_web_page_preview' => true,
            ])->addButtonRow(
                InlineKeyboardButton::make(
                    text: !$this->settings->get('news') ? trans('settings.enable_news') : trans('settings.disable_news'),
                    callback_data: 'settings:news@handleNews'),
                InlineKeyboardButton::make(
                    text: trans('settings.language.title'),
                    callback_data: 'settings:languages@handleLanguages'
                )
            )->addButtonRow(
                InlineKeyboardButton::make(
                    text: trans('settings.watermark.title'),
                    callback_data: 'settings:watermark@handleWatermark'
                ),
                InlineKeyboardButton::make(
                    text: trans('settings.template.change'),
                    callback_data: 'settings:template@handleTemplate'
                )
            )->addButtonRow(
                InlineKeyboardButton::make(
                    text: !$this->settings->get('trim') ? trans('settings.trim.enable') : trans('settings.trim.disable'),
                    callback_data: 'settings:trim@handleTrim'
                ),
            )->addButtonRow(
                InlineKeyboardButton::make(
                    text: '❌ '.trans('common.close'),
                    callback_data: 'settings:cancel@end')
            )->showMenu();

        stats('command.settings');
    }

    protected function handleNews(Nutgram $bot): void
    {
        $this->settings->set('news', !$this->settings->get('news'));

        stats('settings.news', ['status' => $this->settings->get('news')]);

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

    protected function handleWatermark(Nutgram $bot): void
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
            InlineKeyboardButton::make(trans('watermark.opacity.set'),
                callback_data: 'watermark:opacity:set@setOpacity'),
            InlineKeyboardButton::make(trans('watermark.position.set'),
                callback_data: 'watermark:position:set@setPosition')
        );
        $this->addButtonRow(
            InlineKeyboardButton::make(trans('watermark.text.content.set'),
                callback_data: 'watermark:text:content@setTextContent'),
        );
        $this->addButtonRow(
            InlineKeyboardButton::make(trans('watermark.text.size.set'),
                callback_data: 'watermark:text:size@setTextSize'),
            InlineKeyboardButton::make(trans('watermark.text.color.set'),
                callback_data: 'watermark:text:color@setTextColor')
        );
        $this->addButtonRow(
            InlineKeyboardButton::make(trans('watermark.border.size.set'),
                callback_data: 'watermark:border:size@setBorderSize'),
            InlineKeyboardButton::make(trans('watermark.border.color.set'),
                callback_data: 'watermark:border:color@setBorderColor')
        );
        $this->addButtonRow(
            InlineKeyboardButton::make(trans('settings.back'), callback_data: 'watermark:back@start')
        );

        $this->showMenu($this->reopen);
        $this->reopen = false;
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
            $this->settings->set('watermark.opacity', $value);
            $this->reopen = true;
            $this->handleWatermark($bot);
        } catch (UnexpectedValueException|ValidationException) {
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
            $this->reopen = true;
            $this->handleWatermark($bot);
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
        $this->reopen = true;
        $this->handleWatermark($bot);
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
            $this->settings->set('watermark.text.size', $value);
            $this->reopen = true;
            $this->handleWatermark($bot);
        } catch (UnexpectedValueException|ValidationException) {
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
        try {
            $value = strtoupper($bot->message()->text);
            $this->settings->set('watermark.text.color', $value);
            $this->reopen = true;
            $this->handleWatermark($bot);
        } catch (ValidationException) {
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
            $this->settings->set('watermark.border.size', $value);
            $this->reopen = true;
            $this->handleWatermark($bot);
        } catch (UnexpectedValueException|ValidationException) {
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
        try {
            $value = strtoupper($bot->message()->text);
            $this->settings->set('watermark.border.color', $value);
            $this->reopen = true;
            $this->handleWatermark($bot);
        } catch (ValidationException) {
            $bot->sendMessage(trans('common.invalid_value'));
            $this->setBorderColor($bot);
        }
    }

    protected function handleTemplate(Nutgram $bot): void
    {
        $current = $this->settings->get('template');
        $this->settings->set(
            'template',
            $current === StickerTemplate::STICKER() ? StickerTemplate::ICON() : StickerTemplate::STICKER()
        );

        $this->start($bot);
    }

    protected function handleTrim(Nutgram $bot): void
    {
        $this->settings->set('trim', !$this->settings->get('trim'));

        stats('settings.trim', ['status' => $this->settings->get('trim')]);

        $this->start($bot);
    }
}
