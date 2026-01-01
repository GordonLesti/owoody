<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Log;
use App\Entity\Route as EntityRoute;
use App\Enum\Fontainebleau;
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
    #[Route('/logs', name: 'log', methods: ['GET'])]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $user = $this->getUser();
        $logs = array_reverse($user->getLogs()->toArray());
        $sessions = [];
        foreach ($logs as $log) {
            $date = $log->getCreatedAt()->format('D M d o');
            if (!isset($sessions[$date])) {
                $sessions[$date] = [];
            }
            $sessions[$date][] = $log;
        }
        return $this->render('log/index.html.twig', [
            'sessions' => $sessions,
            'logs' => $user->getLogs()->toArray(),
            'grades' => Fontainebleau::cases(),
        ]);
    }

    #[Route('/logs/{id:log}/edit', name: 'log_edit', requirements: ['id' => Requirement::POSITIVE_INT], methods: ['GET', 'POST'])]
    public function edit(Request $request, Log $log, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $form = $this->createForm(LogType::class, $log, [
            'method' => 'POST',
            'action' => $this->generateUrl('log_edit', ['id' => $log->getId()]),
            'attr' => [
                'id' => 'log_form',
            ],
            'route_entity' => $log->getRoute(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($log);
            $entityManager->flush();
            $this->addFlash('success', 'Log updated successfully!');

            return $this->redirectToRoute('log', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('log/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/logs/{id:log}/delete', name: 'log_delete', requirements: ['id' => Requirement::POSITIVE_INT], methods: ['POST'])]
    public function delete(Request $request, Log $log, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $token = (string) $request->getPayload()->get('token');
        if (!$this->isCsrfTokenValid('delete', $token)) {
            $this->addFlash('error', 'Invalid Csrf token.');
            return $this->redirectToRoute('log', [], Response::HTTP_SEE_OTHER);
        }
        $entityManager->remove($log);
        $entityManager->flush();
        $this->addFlash('success', 'Log deleted successfully!');

        return $this->redirectToRoute('log', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/logs/{id:route}/new', name: 'log_new', requirements: ['id' => Requirement::POSITIVE_INT], methods: ['GET', 'POST'])]
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