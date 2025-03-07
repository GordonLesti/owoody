<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Route as EntityRoute;
use App\Enum\Fontainebleau;
use App\Form\RouteType;
use App\Repository\RouteRepository;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

final class RouteController extends AbstractController
{
    #[Route('/', name: 'route', methods: ['GET'])]
    public function index(RouteRepository $routeRepository): Response
    {
        return $this->render('route/index.html.twig', [
            'routes' => $routeRepository->findAll(),
            'grades' => Fontainebleau::cases(),
        ]);
    }

    #[Route('/routes/{id:route}/view', name: 'route_view', requirements: ['id' => Requirement::POSITIVE_INT], methods: ['GET'])]
    public function view(EntityRoute $route, SettingRepository $settingRepository): Response
    {
        $setting = $settingRepository->getLatestSetting();
        $isSymmetric = false;
        if ($setting !== null) {
            $isSymmetric = $setting->isSymmetric();
        }
        return $this->render('route/view.html.twig', [
            'route_entity' => $route,
            'is_symmetric' => $isSymmetric,
        ]);
    }

    #[Route('/routes/{id:route}/edit', name: 'route_edit', requirements: ['id' => Requirement::POSITIVE_INT], methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityRoute $route, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RouteType::class, $route, [
            'method' => 'POST',
            'action' => $this->generateUrl('route_edit', ['id' => $route->getId()]),
            'attr' => [
                'id' => 'hold_setup_form',
            ],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($route);
            $entityManager->flush();
            $this->addFlash('success', 'Route updated successfully!');

            return $this->redirectToRoute('route_view', ['id' => $route->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('route/edit.html.twig', [
            'form' => $form,
            'route_entity' => $route,
        ]);
    }

    #[Route('/routes/new', name: 'route_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $route = new EntityRoute();
        $form = $this->createForm(RouteType::class, $route, [
            'method' => 'POST',
            'action' => $this->generateUrl('route_new'),
            'attr' => [
                'id' => 'hold_setup_form',
            ],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($route);
            $entityManager->flush();
            $this->addFlash('success', 'Route created successfully!');

            return $this->redirectToRoute('route_view', ['id' => $route->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('route/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/routes/{id:route}/delete', name: 'route_delete', requirements: ['id' => Requirement::POSITIVE_INT], methods: ['POST'])]
    public function delete(Request $request, EntityRoute $route, EntityManagerInterface $entityManager): Response
    {
        $token = (string) $request->getPayload()->get('token');
        if (!$this->isCsrfTokenValid('delete', $token)) {
            return $this->redirectToRoute('route', [], Response::HTTP_SEE_OTHER);
        }
        $entityManager->remove($route);
        $entityManager->flush();
        $this->addFlash('success', 'Route deleted successfully!');

        return $this->redirectToRoute('route', [], Response::HTTP_SEE_OTHER);
    }
}