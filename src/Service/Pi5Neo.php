<?php

declare(strict_types=1);

namespace App\Service;

use InvalidArgumentException;
use OutOfBoundsException;

class Pi5Neo implements PixelChainInterface
{
    /** @var int[] */
    private array $pixels = [];

    public function __construct(
        private readonly int $pixelNum
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
        $maxLimit = 0x1000000;
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
        $cmd = "python3 -c \"from pi5neo import Pi5Neo; ";
        $cmd .= sprintf(
            "neo = Pi5Neo('/dev/spidev0.0', %d, 800); ",
            $this->pixelNum
        );
        foreach ($this->pixels as $index => $color) {
            $cmd .= sprintf("neo.set_led_color(%d, %d, %d, %d); ", $index, ...$this->hexToRgb($color));
        }
        $cmd .= "neo.update_strip();\"";
        shell_exec($cmd);
    }

    private function hexToRgb(int $hex): array
    {
        return [
            ($hex >> 16) & 0xFF,
            ($hex >> 8) & 0xFF,
            $hex & 0xFF
        ];
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