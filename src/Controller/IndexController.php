<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\Fontainebleau;
use App\Repository\RouteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(RouteRepository $routeRepository): Response
    {
        $routes = $routeRepository->findAll();
        $grades = Fontainebleau::cases();
        $easiestKey = count($grades) - 1;
        $hardestKey = 0;
        foreach ($routes as $route) {
            $gradeKey = array_search($route->getGrade(), $grades);
            if ($gradeKey < $easiestKey) {
                $easiestKey = $gradeKey;
            }
            if ($gradeKey > $hardestKey) {
                $hardestKey = $gradeKey;
            }
        }
        $routeCount = count($routes);
        return $this->render('index.html.twig', [
            'route_count' => $routeCount,
            'easiest' => $routeCount ? $grades[$easiestKey] : null,
            'hardest' => $routeCount ? $grades[$hardestKey] : null,
        ]);
    }
}