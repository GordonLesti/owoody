<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @implements DataTransformerInterface<array, string>
 */
final readonly class JsonTransformer implements DataTransformerInterface
{
    /**
     * @param mixed[]|null $value
     * @return string
     */
    public function transform(mixed $value): mixed
    {
        $decoded = json_encode($value);
        if ($decoded === false) {
            return '[]';
        }
        return $decoded;
    }

    /**
     * @param string $value
     * @return mixed[]
     */
    public function reverseTransform(mixed $value): array
    {
        if (empty($value)) {
            return [];
        }
        return json_decode($value, true);
    }
}