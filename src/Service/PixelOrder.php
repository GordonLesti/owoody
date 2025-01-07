<?php

declare(strict_types=1);

namespace App\Service;

enum PixelOrder: string
{
    case RGB = 'RGB';
    case GRB = 'GRB';
    case RGBW = 'RGBW';
    case GRBW = 'GRBW';
}