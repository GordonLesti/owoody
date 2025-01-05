<?php
declare(strict_types=1);

namespace App\Service;

use ArrayAccess;
use InvalidArgumentException;
use OutOfBoundsException;

class NeoPixel implements ArrayAccess
{
    private array $pixels = [];

    public function __construct(private readonly int $pixelNum)
    {
    }

    public function offsetExists(mixed $offset): bool
    {
        return is_int($offset) && $offset >= 0 && $offset < $this->pixelNum;
    }

    public function offsetGet(mixed $offset): mixed
    {
        if (!$this->offsetExists($offset)) {
            throw new OutOfBoundsException('Illegal offset given.');
        }
        if (isset($this->pixels[$offset])) {
            return '#' . $this->pixels[$offset];
        }
        return null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!$this->offsetExists($offset)) {
            throw new OutOfBoundsException('Illegal offset given.');
        }
        if ($value === null) {
            $this->offsetUnset($offset);
            return;
        }
        if (!is_string($value) || preg_match('/^#(?:[0-9a-fA-F]{3}){1,2}$/', $value) !== 1) {
            throw new InvalidArgumentException('Value needs to be hex color in format ^#(?:[0-9a-fA-F]{3}){1,2}$.');
        }
        $this->pixels[$offset] = substr(strtoupper($value), 1);
    }

    public function offsetUnset(mixed $offset): void
    {
        if (!$this->offsetExists($offset)) {
            throw new OutOfBoundsException('Illegal offset given.');
        }
        unset($this->pixels[$offset]);
    }

    public function show(): void
    {
        $cmd = "python3 -c \"import board\n";
        $cmd .= "import neopixel_spi as neopixel\n";
        $cmd .= sprintf(
            "pixels = neopixel.NeoPixel_SPI(board.SPI(), %d, pixel_order=neopixel.GRB, auto_write=False)\n",
            $this->pixelNum
        );
        foreach ($this->pixels as $index => $color) {
            $cmd .= sprintf("pixels[%d] = 0x%s\n", $index, $color);
        }
        $cmd .= "pixels.show()\"";
        shell_exec($cmd);
    }

    public function fill(?string $value): void
    {
        $this->pixels = array_fill(0, $this->pixelNum, $value);
    }
}