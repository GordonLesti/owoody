<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Route;
use App\Entity\User;
use App\Enum\Fontainebleau;
use App\Repository\LogRepository;

class LogAccumulator
{
    private array $gradeScale;

    public function __construct(
        private readonly LogRepository $logRepo
    ) {
        $this->gradeScale = Fontainebleau::cases();
    }

    public function getAccumulatedData(Route $route, ?User $user = null): array
    {
        $groupedGrades = [];
        $groupedRatings = [];
        $userCount = [];
        foreach ($this->logRepo->findBy(['route' => $route]) as $log) {
            $angle = $log->getAngle();
            if (!isset($groupedGrades[$angle])) {
                $groupedGrades[$angle] = [];
            }
            $groupedGrades[$angle][] = $this->transerGradeToNumber($log->getGrade());
            if (!isset($groupedRatings[$angle])) {
                $groupedRatings[$angle] = [];
            }
            $groupedRatings[$angle][] = $log->getRating();
            if ($user !== null && $user->getId() === $log->getUser()->getId()) {
                if (!isset($userCount[$angle])) {
                    $userCount[$angle] = [0, 0];
                }
                $userCount[$angle][$log->isMirrored() ? 1 : 0]++;
            }
        }
        $accGrades = [];
        $logCount = [];
        foreach ($groupedGrades as $angle => $gradeSets) {
            $accGrades[$angle] = $this->transferNumberToGrade((int)round(array_sum($gradeSets) / count($gradeSets)));
            $logCount[$angle] = count($gradeSets);
        }
        $accRatings = [];
        foreach ($groupedRatings as $angle => $ratingSets) {
            $accRatings[$angle] = round(array_sum($ratingSets) / count($ratingSets));
        }
        $result = [
            'grades' => $accGrades,
            'ratings' => $accRatings,
            'counts' => $logCount,
        ];
        if ($user !== null) {
            $result['user_counts'] = $userCount;
        }
        return $result;
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