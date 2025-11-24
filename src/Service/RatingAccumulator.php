<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Route;
use App\Repository\LogRepository;

class RatingAccumulator
{
    public function __construct(
        private readonly LogRepository $logRepo
    ) {
    }

    public function getAccumulatedRatings(Route $route): array
    {
        $groupedRatings = [];
        foreach ($this->logRepo->findBy(['route' => $route]) as $log) {
            $angle = $log->getAngle();
            if (!isset($groupedRatings[$angle])) {
                $groupedRatings[$angle] = [];
            }
            $groupedRatings[$angle][] = $log->getRating();
        }
        $accRatings = [];
        foreach ($groupedRatings as $angle => $ratingSets) {
            $accRatings[$angle] = round(array_sum($ratingSets) / count($ratingSets));
        }
        return $accRatings;
    }
}