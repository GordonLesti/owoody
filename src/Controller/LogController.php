<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Log;
use App\Entity\Route as EntityRoute;
use App\Form\LogType;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

final class LogController extends AbstractController
{
    #[Route('/log/{id:route}/new', name: 'log_new', requirements: ['id' => Requirement::POSITIVE_INT], methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityRoute $route,
        EntityManagerInterface $entityManager,
        SettingRepository $settingRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $log = new Log();
        $log->setUser($this->getUser());
        $log->setRoute($route);
        $form = $this->createForm(LogType::class, $log, [
            'method' => 'POST',
            'action' => $this->generateUrl('log_new', ['id' => $route->getId()]),
            'attr' => [
                'id' => 'log_form',
            ],
            'route_entity' => $route,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($log);
                $entityManager->flush();
                $this->addFlash('success', 'Log successfully!');
                return $this->redirectToRoute('route_view', ['id' => $route->getId()], Response::HTTP_SEE_OTHER);
            }
        }
        $setting = $settingRepository->getLatestSetting();
        $isAdjustable = false;
        if ($setting !== null) {
            $isAdjustable = $setting->isAdjustable();
        }
        return $this->render('log/new.html.twig', [
            'form' => $form,
            'route_entity' => $route,
            'is_adjustable' => $isAdjustable,
        ]);
    }
}