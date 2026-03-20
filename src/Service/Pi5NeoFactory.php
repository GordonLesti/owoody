<?php

declare(strict_types=1);

namespace App\Service;

class Pi5NeoFactory implements PixelChainFactoryInterface
{
    public function create(int $pixelNum): PixelChainInterface
    {
        return new Pi5Neo($pixelNum);
    }
}