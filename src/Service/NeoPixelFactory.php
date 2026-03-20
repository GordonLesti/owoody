<?php

declare(strict_types=1);

namespace App\Service;

class NeoPixelFactory implements PixelChainFactoryInterface
{
    public function create(int $pixelNum): PixelChainInterface
    {
        return new NeoPixel($pixelNum);
    }
}