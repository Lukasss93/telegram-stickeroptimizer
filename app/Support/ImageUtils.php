<?php

namespace App\Support;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Exception;
use File;
use InvalidArgumentException;
use Throwable;

class ImageUtils
{
    /**
     * Validate hex color
     * @param string $value
     * @return bool
     */
    public function isHexColor(string $value): bool
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
    public function isAnAnimatedWebp(mixed $source): bool
    {
        if (is_string($source)) {
            return str_contains(file_get_contents($source), 'ANMF');
        }

        if (is_resource($source)) {
            return str_contains(stream_get_contents($source), 'ANMF');
        }

        throw new InvalidArgumentException('The source parameter must be string or resource.');
    }

    /**
     * Generate a QR code
     * @param string $content
     * @param string|null $name
     * @param bool $asResource
     * @return mixed
     */
    public function qrcode(string $content, string $name = null, bool $asResource = false): mixed
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
}
