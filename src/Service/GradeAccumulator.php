<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Route;
use App\Enum\Fontainebleau;
use App\Repository\LogRepository;

class GradeAccumulator
{
    private array $gradeScale;

    public function __construct(
        private readonly LogRepository $logRepo
    ) {
        $this->gradeScale = Fontainebleau::cases();
    }

    public function getAccumulatedGradings(Route $route): array
    {
        $groupedRatings = [];
        foreach ($this->logRepo->findBy(['route' => $route]) as $log) {
            $angle = $log->getAngle();
            if (!isset($groupedRatings[$angle])) {
                $groupedRatings[$angle] = [];
            }
            $groupedRatings[$angle][] = $this->transerGradeToNumber($log->getGrade());
        }
        $accRatings = [];
        foreach ($groupedRatings as $angle => $ratingSets) {
            $accRatings[$angle] = $this->transferNumberToGrade((int)round(array_sum($ratingSets) / count($ratingSets)));
        }
        return $accRatings;
    }

    private function transerGradeToNumber(Fontainebleau $grade): int
    {
        return array_search($grade, $this->gradeScale);
    }

    private function transferNumberToGrade(int $number): Fontainebleau
    {
        return $this->gradeScale[$number];
    }
}