<?php

declare(strict_types=1);

namespace App\Enum;

enum LedHoldType: int
{
    case HAND = 1;
    case FINISH = 2;
    case FOOT = 3;
    case START = 4;
}