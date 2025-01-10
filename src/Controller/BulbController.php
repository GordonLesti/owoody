<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\LedHoldType;
use App\Service\NeoPixel;
use App\Service\PixelOrder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BulbController extends AbstractController
{
    #[Route('/bulb', name: 'bulb', format: 'json', methods: ['POST'])]
    public function index(Request $request): Response
    {
        $colors = [
            LedHoldType::NONE->value => null,
            LedHoldType::HAND->value => 0x0000FF,
            LedHoldType::FINISH->value => 0xFF0000,
            LedHoldType::FOOT->value => 0xFFFF00,
            LedHoldType::START->value => 0x00FF00,
        ];
        $pixelConfig = array_reverse($request->toArray());
        $flattenPixelConfig = array_merge(...$pixelConfig);
        $neoPixels = new NeoPixel(count($flattenPixelConfig), PixelOrder::RGB);
        foreach (array_filter($flattenPixelConfig) as $index => $pixel) {
            $neoPixels[$index] = $colors[$pixel];
        }
        $neoPixels->show();
        return $this->json([]);
    }
}