<?php

declare(strict_types=1);

namespace App\Enum;

enum LedHoldType: int
{
    case START = 1;
    case HAND = 2;
    case FOOT = 3;
    case FINISH = 4;
}