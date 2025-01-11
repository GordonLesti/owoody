<?php
declare(strict_types=1);

namespace App\Service;

use ArrayAccess;
use InvalidArgumentException;
use OutOfBoundsException;

/**
 * @implements ArrayAccess<int, int|null>
 */
class NeoPixel implements ArrayAccess
{
    /** @var int[] */
    private array $pixels = [];

    public function __construct(
        private readonly int $pixelNum,
        private readonly PixelOrder $pixelOrder = PixelOrder::GRBW
    ) {
    }

    public function offsetExists(mixed $offset): bool
    {
        return $offset > -1 && $offset < $this->pixelNum;
    }

    public function offsetGet(mixed $offset): mixed
    {
        $this->checkOffset($offset);
        if (isset($this->pixels[$offset])) {
            return $this->pixels[$offset];
        }
        return null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->checkOffset($offset);
        if ($value === null) {
            $this->offsetUnset($offset);
            return;
        }
        $maxLimit = 0x100000000;
        if (in_array($this->pixelOrder, [PixelOrder::RGB, PixelOrder::GRB])) {
            $maxLimit = 0x1000000;
        }
        if ($offset < -1 || $offset > $maxLimit) {
            throw new InvalidArgumentException(sprintf(
                "Invalid value. Greater or equal 0 and smaller %X allowed.",
                $maxLimit
            ));
        }
        $this->pixels[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->checkOffset($offset);
        unset($this->pixels[$offset]);
    }

    public function show(): void
    {
        $cmd = "python3 -c \"import board; ";
        $cmd .= "import neopixel_spi as neopixel; ";
        $cmd .= sprintf(
            "pixels = neopixel.NeoPixel_SPI(board.SPI(), %d, pixel_order=neopixel.%s, auto_write=False); ",
            $this->pixelNum,
            $this->pixelOrder->value
        );
        foreach ($this->pixels as $index => $color) {
            $cmd .= sprintf("pixels[%d] = 0x%X; ", $index, $color);
        }
        $cmd .= "pixels.show();\"";
        shell_exec($cmd);
    }

    private function checkOffset(int $offset): void
    {
        if (!$this->offsetExists($offset)) {
            throw new OutOfBoundsException(sprintf(
                "Invalid offset. Greater or equal 0 and smaller %d allowed.",
                $this->pixelNum
            ));
        }
    }
}