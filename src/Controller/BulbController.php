<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\NeoPixel;
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
            null,
            '#0000FF',
            '#FF0000',
            '#FFFF00',
            '#00FF00',
        ];
        $pixelConfig = $request->toArray();
        $flattenPixelConfig = array_merge(...$pixelConfig);
        $neoPixels = new NeoPixel(count($flattenPixelConfig));
        foreach ($flattenPixelConfig as $index => $pixel) {
            $neoPixels[$index] = $colors[$pixel];
        }
        $neoPixels->show();
        return $this->json($request->toArray());
    }
}