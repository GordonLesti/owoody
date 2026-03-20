<?php

declare(strict_types=1);

namespace App\Service;

use ArrayAccess;

/**
 * @implements ArrayAccess<int, int|null>
 */
interface PixelChainInterface extends ArrayAccess
{
    public function show(): void;
}
