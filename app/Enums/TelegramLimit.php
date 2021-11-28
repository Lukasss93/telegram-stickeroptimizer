<?php

namespace App\Enums;

enum TelegramLimit: int
{
    case DOWNLOAD = 20971520;
    case STICKER_MAX_SIZE = 524288;
}
