<?php

declare(strict_types=1);

namespace App\Service;

interface PixelChainFactoryInterface
{
    public function create(int $pixelNum): PixelChainInterface;
}