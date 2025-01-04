<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

final readonly class JsonTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): mixed
    {
        if (empty($value)) {
            return json_encode([]);
        }
        return json_encode($value);
    }

    public function reverseTransform(mixed $value): array
    {
        if (empty($value)) {
            return [];
        }
        return json_decode($value, true);
    }
}