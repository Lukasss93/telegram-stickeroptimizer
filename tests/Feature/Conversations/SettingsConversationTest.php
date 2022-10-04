<?php

use App\Enums\StickerTemplate;
use App\Enums\WatermarkPosition;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Attributes\UpdateTypes;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Testing\FakeNutgram;

beforeEach(function () {
    $this->mainMenu = function (): FakeNutgram {
        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(InlineKeyboardButton::make(
                text: trans('settings.disable_news'),
                callback_data: 'settings:news')
            )
            ->addRow(
                InlineKeyboardButton::make(
                    text: trans('settings.language.title'),
                    callback_data: 'settings:languages'
                )
            )->addRow(
                InlineKeyboardButton::make(
                    text: trans('settings.watermark.title'),
                    callback_data: 'settings:watermark'
                )
            )
            ->addRow(
                InlineKeyboardButton::make(
                    text: trans('settings.template.change'),
                    callback_data: 'settings:template'
                )
            )->addRow(
                InlineKeyboardButton::make(
                    text: '❌ '.trans('common.close'),
                    callback_data: 'settings:cancel')
            );

        $test = bot()
            ->willStartConversation()
            ->hearUpdateType(UpdateTypes::MESSAGE, [
                'text' => '/settings',
                'from' => [
                    'id' => $this->chat->chat_id,
                    'first_name' => $this->chat->first_name,
                    'last_name' => $this->chat->last_name,
                    'username' => $this->chat->username,
                ],
                'chat' => ['id' => $this->chat->chat_id],
            ])
            ->reply()
            ->assertReplyMessage([
                'text' => message('settings.main', [
                    'news' => $this->chat->settings()->get('news'),
                    'language' => language($this->chat->settings()->get('language')),
                    'watermark' => $this->chat->settings()->get('watermark.opacity') > 0,
                    'template' => StickerTemplate::from($this->chat->settings()->get('template'))->getLabel(),
                ]),
                'parse_mode' => ParseMode::HTML,
                'disable_web_page_preview' => true,
                'reply_markup' => $keyboard,
            ])
            ->assertActiveConversation();

        $this->assertDatabaseHas('statistics', [
            'action' => 'settings',
            'category' => 'command',
        ]);

        return $test;
    };
    $this->languageMenu = function (): FakeNutgram {
        $keyboard = InlineKeyboardMarkup::make();
        collect(language())
            ->map(fn ($item, $key) => InlineKeyboardButton::make($item, callback_data: "language:$key"))
            ->chunk(2)
            ->each(fn ($row) => $keyboard->addRow(...$row->values()));
        $keyboard->addRow(InlineKeyboardButton::make(trans('settings.back'), callback_data: 'languages:back'));

        return ($this->mainMenu)()
            ->hearCallbackQueryData('settings:languages')
            ->reply()
            ->assertReply('editMessageText', [
                'text' => message('settings.language', [
                    'language' => language($this->chat->settings()->get('language')),
                    'localization' => config('bot.localization'),
                ]),
                'parse_mode' => ParseMode::HTML,
                'disable_web_page_preview' => true,
                'reply_markup' => $keyboard,
            ])
            ->assertReply('answerCallbackQuery', index: 1)
            ->assertActiveConversation();
    };
    $this->watermarkMenu = function (): FakeNutgram {
        return ($this->mainMenu)()
            ->hearCallbackQueryData('settings:watermark')
            ->reply()
            ->assertReply('editMessageText', [
                'text' => message('settings.watermark', [
                    'opacity' => $this->chat->settings()->get('watermark.opacity'),
                    'position' => WatermarkPosition::tryFrom($this->chat->settings()->get('watermark.position'))?->emoji(),
                    'textContent' => $this->chat->settings()->get('watermark.text.content'),
                    'textSize' => $this->chat->settings()->get('watermark.text.size'),
                    'textColor' => $this->chat->settings()->get('watermark.text.color'),
                    'borderSize' => $this->chat->settings()->get('watermark.border.size'),
                    'borderColor' => $this->chat->settings()->get('watermark.border.color'),
                ]),
                'parse_mode' => ParseMode::HTML,
                'disable_web_page_preview' => true,
                'reply_markup' => InlineKeyboardMarkup::make()
                    ->addRow(
                        InlineKeyboardButton::make(trans('watermark.opacity.set'),
                            callback_data: 'watermark:opacity:set'),
                        InlineKeyboardButton::make(trans('watermark.position.set'),
                            callback_data: 'watermark:position:set')
                    )
                    ->addRow(
                        InlineKeyboardButton::make(
                            trans('watermark.text.content.set'),
                            callback_data: 'watermark:text:content'
                        ),
                    )
                    ->addRow(
                        InlineKeyboardButton::make(trans('watermark.text.size.set'),
                            callback_data: 'watermark:text:size'),
                        InlineKeyboardButton::make(trans('watermark.text.color.set'),
                            callback_data: 'watermark:text:color')
                    )
                    ->addRow(
                        InlineKeyboardButton::make(
                            trans('watermark.border.size.set'),
                            callback_data: 'watermark:border:size'
                        ),
                        InlineKeyboardButton::make(
                            trans('watermark.border.color.set'),
                            callback_data: 'watermark:border:color'
                        )
                    )
                    ->addRow(
                        InlineKeyboardButton::make(trans('settings.back'), callback_data: 'watermark:back')
                    ),
            ])
            ->assertReply('answerCallbackQuery', index: 1)
            ->assertActiveConversation();
    };
});

it('sends /settings command', function () {
    botFromCallable($this->mainMenu);
});

it('clicks on news button', function () {
    botFromCallable($this->mainMenu)
        ->hearCallbackQueryData('settings:news')
        ->reply()
        ->assertReply('editMessageText')
        ->assertReply('answerCallbackQuery', index: 1)
        ->assertActiveConversation();

    expect($this->chat->settings()->get('news'))->toBe(false);

    $this->assertDatabaseHas('statistics', [
        'action' => 'news action',
        'category' => 'settings',
        'value' => json_encode(['status' => $this->chat->settings()->get('news')]),
    ]);
});

it('clicks on language button', function () {
    botFromCallable($this->languageMenu);
});

it('sets the bot language', function () {
    botFromCallable($this->languageMenu)
        ->hearCallbackQueryData('language:it')
        ->reply()
        ->assertRaw(fn ($i) => $this->chat->settings()->get('language') === 'it')
        ->assertRaw(fn ($i) => app()->getLocale() === 'it')
        ->assertReply('answerCallbackQuery', index:1)
        ->assertActiveConversation();
});

it('clicks on watermark button', function () {
    botFromCallable($this->watermarkMenu);
});

it('sets the watermark opacity', function () {
    botFromCallable($this->watermarkMenu)
        ->hearCallbackQueryData('watermark:opacity:set')
        ->reply()
        ->assertReply('deleteMessage')
        ->assertReplyText(trans('watermark.opacity.send'), 1)
        ->assertReply('answerCallbackQuery', index: 2)
        ->hearText(50)
        ->reply()
        ->assertReply('deleteMessage')
        ->assertReply('sendMessage', index: 1);

    expect($this->chat->settings()->get('watermark.opacity'))->toBe(50);
});

it('sets the watermark position', function () {
    botFromCallable($this->watermarkMenu)
        ->hearCallbackQueryData('watermark:position:set')
        ->reply()
        ->assertReply('deleteMessage')
        ->assertReplyText(trans('watermark.position.send'), 1)
        ->assertReply('answerCallbackQuery', index: 2)
        ->hearText(WatermarkPosition::MIDDLE_CENTER->emoji())
        ->reply()
        ->assertReply('deleteMessage')
        ->assertReply('sendMessage', index: 1);

    expect($this->chat->settings()->get('watermark.position'))->toBe(WatermarkPosition::MIDDLE_CENTER->value);
});

it('sets the watermark text content', function () {
    botFromCallable($this->watermarkMenu)
        ->hearCallbackQueryData('watermark:text:content')
        ->reply()
        ->assertReply('deleteMessage')
        ->assertReplyText(trans('watermark.text.content.send'), 1)
        ->assertReply('answerCallbackQuery', index: 2)
        ->hearText('Hello there!')
        ->reply()
        ->assertReply('deleteMessage')
        ->assertReply('sendMessage', index: 1);

    expect($this->chat->settings()->get('watermark.text.content'))->toBe('Hello there!');
});

it('sets the watermark text size', function () {
    botFromCallable($this->watermarkMenu)
        ->hearCallbackQueryData('watermark:text:size')
        ->reply()
        ->assertReply('deleteMessage')
        ->assertReplyText(trans('watermark.text.size.send'), 1)
        ->assertReply('answerCallbackQuery', index: 2)
        ->hearText(20)
        ->reply()
        ->assertReply('deleteMessage')
        ->assertReply('sendMessage', index: 1);

    expect($this->chat->settings()->get('watermark.text.size'))->toBe(20);
});

it('sets the watermark text color', function () {
    botFromCallable($this->watermarkMenu)
        ->hearCallbackQueryData('watermark:text:color')
        ->reply()
        ->assertReply('deleteMessage')
        ->assertReplyText(trans('watermark.text.color.send'), 1)
        ->assertReply('answerCallbackQuery', index: 2)
        ->hearText('#000000')
        ->reply()
        ->assertReply('deleteMessage')
        ->assertReply('sendMessage', index: 1);

    expect($this->chat->settings()->get('watermark.text.color'))->toBe('#000000');
});

it('sets the watermark border size', function () {
    botFromCallable($this->watermarkMenu)
        ->hearCallbackQueryData('watermark:border:size')
        ->reply()
        ->assertReply('deleteMessage')
        ->assertReplyText(trans('watermark.border.size.send'), 1)
        ->assertReply('answerCallbackQuery', index: 2)
        ->hearText(5)
        ->reply()
        ->assertReply('deleteMessage')
        ->assertReply('sendMessage', index: 1);

    expect($this->chat->settings()->get('watermark.border.size'))->toBe(5);
});

it('sets the watermark border color', function () {
    botFromCallable($this->watermarkMenu)
        ->hearCallbackQueryData('watermark:border:color')
        ->reply()
        ->assertReply('deleteMessage')
        ->assertReplyText(trans('watermark.border.color.send'), 1)
        ->assertReply('answerCallbackQuery', index: 2)
        ->hearText('#000000')
        ->reply()
        ->assertReply('deleteMessage')
        ->assertReply('sendMessage', index: 1);

    expect($this->chat->settings()->get('watermark.border.color'))->toBe('#000000');
});

it('sets the template "Icon"', function () {
    botFromCallable($this->mainMenu)
        ->hearCallbackQueryData('settings:template')
        ->reply()
        ->assertReply('editMessageText')
        ->assertReply('answerCallbackQuery', index: 1)
        ->assertActiveConversation();

    expect($this->chat->settings()->get('template'))->toBe(StickerTemplate::ICON());
});

it('closes the settings menu', function () {
    botFromCallable($this->mainMenu)
        ->hearCallbackQueryData('settings:cancel')
        ->reply()
        ->assertReply('deleteMessage')
        ->assertReply('answerCallbackQuery', index: 1)
        ->assertNoConversation();
});
