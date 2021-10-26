<?php

namespace App\Enums;

use App\Contracts\Enum;

class TelegramLimit extends Enum
{
    public const DOWNLOAD = 20971520;
    public const STICKER_MAX_SIZE = 524288;
}
