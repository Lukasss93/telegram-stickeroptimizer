<?php

use App\Models\Statistic;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;

/**
 * Return a formatted string (in C# like)
 * @param string $string
 * @param array $args
 * @return string
 */
function f(string $string, array $args = []): string
{
    preg_match_all('/(?={){(\d+)}(?!})/', $string, $matches, PREG_OFFSET_CAPTURE);
    $offset = 0;
    foreach ($matches[1] as $data) {
        $i = $data[0];
        $string = substr_replace($string, @$args[$i], $offset + $data[1] - 1, 2 + strlen($i));
        $offset += strlen(@$args[$i]) - 2 - strlen($i);
    }

    return $string;
}

/**
 * Render an HTML message
 * @param string $view
 * @param array $values
 * @return string
 */
function message(string $view, array $values = []): string
{
    return rescue(function () use ($view, $values) {
        return (string)Str::of(view("messages.$view", $values)->render())
            ->replaceMatches('/\r\n|\r|\n/', '')
            ->replace(['<br>', '<BR>'], "\n");
    }, 'messages.'.$view);
}

/**
 * Dump a message to dev chat
 * @param $message
 * @throws JsonException
 */
function dt($message): void
{
    if (is_iterable($message)) {
        $message = json_encode(
            $message,
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        );
    }

    $bot = app(Nutgram::class);
    $bot->sendMessage("<b>Debug:</b>\n<pre>$message</pre>", [
        'chat_id' => config('developer.id'),
        'parse_mode' => ParseMode::HTML,
    ]);
}

/**
 * Return a language name by code or a list of languages name
 * @param string|null $code
 * @return array|string
 * @throws InvalidArgumentException
 */
function language(string $code = null): array|string
{
    $languages = config('languages');

    if ($code === null) {
        return $languages;
    }

    if (!array_key_exists($code, $languages)) {
        throw new InvalidArgumentException('Language code not found');
    }

    return $languages[$code];
}

/**
 * Cast a value
 * @param string $type
 * @param mixed $value
 * @param mixed|null $default
 * @return array|bool|float|int|object|string
 */
function cast(string $type, mixed $value, mixed $default = null): array|bool|float|int|object|string
{
    if ($value === '' || $value === null) {
        return $default;
    }

    return match ($type) {
        'int', 'integer' => (int)$value,
        'real', 'float', 'double' => (float)$value,
        'string' => (string)$value,
        'bool', 'boolean' => (bool)$value,
        'object' => (object)$value,
        'array' => (array)$value,
        default => $value,
    };
}

/**
 * Generate a QR code
 * @param string $content
 * @param string|null $name
 * @param bool $asResource
 * @return mixed
 */
function qrcode(string $content, string $name = null, bool $asResource = false): mixed
{
    $path = storage_path("app/temp/$name.png");

    if (File::exists($path)) {
        return $asResource ? fopen($path, 'rb') : $path;
    }

    try {
        $qrcode = new QRCode(new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'imageBase64' => false,
            'imageTransparent' => false,
            'scale' => 6,
            'eccLevel' => QRCode::ECC_H,
        ]));
        $qrcode->render($content, $path);

        if (!File::exists($path)) {
            throw new Exception('QR code not found.');
        }

        return $asResource ? fopen($path, 'rb') : $path;

    } catch (Throwable) {
        return null;
    }
}

/**
 * Save bot statistic
 * @param string $action
 * @param string|null $category
 * @param array|null $value
 * @param int|null $chat_id
 */
function stats(string $action, string $category = null, array $value = null, int $chat_id = null): void
{
    Statistic::create([
        'action' => $action,
        'category' => $category,
        'value' => $value,
        'chat_id' => $chat_id ?? app(Nutgram::class)->update()?->getChat()?->id,
    ]);
}

/**
 * Zip a folder
 * @param string $source
 * @param string $destination
 * @return bool
 */
function zip(string $source, string $destination): bool
{
    if (!File::exists($source)) {
        throw new RuntimeException('folder not found');
    }

    $zip = new ZipArchive();
    if ($zip->open($destination, ZIPARCHIVE::CREATE)) {
        $source = realpath($source);
        if (is_dir($source)) {
            $iterator = new RecursiveDirectoryIterator($source);
            // skip dot files while iterating
            $iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
            foreach ($files as $file) {
                $file = realpath($file);
                if (is_dir($file)) {
                    $zip->addEmptyDir(str_replace($source.'/', '', $file.'/'));
                } else {
                    if (is_file($file)) {
                        $zip->addFromString(str_replace($source.'/', '', $file), file_get_contents($file));
                    }
                }
            }
        } elseif (is_file($source)) {
            $zip->addFromString(basename($source), file_get_contents($source));
        }
    }

    return $zip->close();
}

/**
 * Validate hex color
 * @param string $value
 * @return bool
 */
function isHexColor(string $value): bool
{
    if (!preg_match('/^#[a-f0-9]{6}$/i', $value)) {
        return false;
    }

    return true;
}

/**
 * Check if a file is an animated webp
 * @param string|resource $source
 * @return bool
 */
function isAnAnimatedWebp(mixed $source): bool
{
    if (is_string($source)) {
        return str_contains(file_get_contents($source), 'ANMF');
    }

    if (is_resource($source)) {
        return str_contains(stream_get_contents($source), 'ANMF');
    }

    throw new InvalidArgumentException('The source parameter must be string or resource.');
}
