<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Log;
use App\Entity\Route as EntityRoute;
use App\Enum\Fontainebleau;
use App\Form\LogType;
use App\Form\RouteType;
use App\Repository\RouteRepository;
use App\Repository\SettingRepository;
use App\Service\LogAccumulator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

final class RouteController extends AbstractController
{
    #[Route('/routes', name: 'route', methods: ['GET'])]
    public function index(
        RouteRepository $routeRepository,
        SettingRepository $settingRepository,
        LogAccumulator $logAcc,
    ): Response {
        $setting = $settingRepository->getLatestSetting();
        $isSymmetric = false;
        $isAdjustable = false;
        if ($setting !== null) {
            $isSymmetric = $setting->isSymmetric();
            $isAdjustable = $setting->isAdjustable();
        }
        $routes = $routeRepository->findAll();
        $grades = [];
        $ratings = [];
        $logCounts = [];
        $userCounts = [];
        $user = $this->getUser();
        foreach ($routes as $route) {
            $accData = $logAcc->getAccumulatedData($route, $user);
            $routeId = $route->getId();
            $grades[$routeId] = $accData['grades'];
            $ratings[$routeId] = $accData['ratings'];
            $logCounts[$routeId] = $accData['counts'];
            if (isset($accData['user_counts'])) {
                $userCounts[$routeId] = $accData['user_counts'];
            }
        }
        $parameters = [
            'routes' => $routes,
            'grades' => Fontainebleau::cases(),
            'is_symmetric' => $isSymmetric,
            'is_adjustable' => $isAdjustable,
            'acc_grades' => $grades,
            'acc_ratings' => $ratings,
            'log_counts' => $logCounts,
        ];
        if ($user !== null) {
            $parameters['user_counts'] = $userCounts;
            $bookmarks = [];
            foreach ($user->getBookmarks() as $item) {
                $bookmarks[] = $item->getId();
            }
            $parameters['bookmarks'] = $bookmarks;
        }
        return $this->render('route/index.html.twig', $parameters);
    }

    #[Route('/routes/{id:route}/view', name: 'route_view', requirements: ['id' => Requirement::POSITIVE_INT], methods: ['GET'])]
    public function view(
        EntityRoute $route,
        SettingRepository $settingRepository,
        LogAccumulator $logAcc,
    ): Response {
        $setting = $settingRepository->getLatestSetting();
        $isSymmetric = false;
        $isAdjustable = false;
        if ($setting !== null) {
            $isSymmetric = $setting->isSymmetric();
            $isAdjustable = $setting->isAdjustable();
        }
        $user = $this->getUser();
        $accData = $logAcc->getAccumulatedData($route, $user);
        $parameters = [
            'route_entity' => $route,
            'grades' => Fontainebleau::cases(),
            'is_symmetric' => $isSymmetric,
            'is_adjustable' => $isAdjustable,
            'acc_grades' => $accData['grades'],
            'acc_ratings' => $accData['ratings'],
            'log_counts' => $accData['counts'],
        ];
        if ($user !== null) {
            $parameters['is_in_bookmark'] = $user->getBookmarks()->contains($route);
        }
        if (isset($accData['user_counts'])) {
            $parameters['user_counts'] = $accData['user_counts'];
        }
        return $this->render('route/view.html.twig', $parameters);
    }

    #[Route('/routes/{id:route}/edit', name: 'route_edit', requirements: ['id' => Requirement::POSITIVE_INT], methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EntityRoute $route,
        EntityManagerInterface $entityManager,
        SettingRepository $settingRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $form = $this->createForm(RouteType::class, $route, [
            'method' => 'POST',
            'action' => $this->generateUrl('route_edit', ['id' => $route->getId()]),
            'attr' => [
                'id' => 'hold_setup_form',
            ],
            'route_entity' => $route,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($route);
            $entityManager->flush();
            $this->addFlash('success', 'Route updated successfully!');

            return $this->redirectToRoute('route_view', ['id' => $route->getId()], Response::HTTP_SEE_OTHER);
        }
        $setting = $settingRepository->getLatestSetting();
        $isAdjustable = false;
        if ($setting !== null) {
            $isAdjustable = $setting->isAdjustable();
        }
        return $this->render('route/edit.html.twig', [
            'form' => $form,
            'route_entity' => $route,
            'is_adjustable' => $isAdjustable,
        ]);
    }

    #[Route('/routes/new', name: 'route_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SettingRepository $settingRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $route = new EntityRoute();
        $form = $this->createForm(RouteType::class, $route, [
            'method' => 'POST',
            'action' => $this->generateUrl('route_new'),
            'attr' => [
                'id' => 'hold_setup_form',
            ],
        ]);
        $route->setRouteSetter($this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($route);
            $entityManager->flush();
            $this->addFlash('success', 'Route created successfully!');

            return $this->redirectToRoute('route_view', ['id' => $route->getId()], Response::HTTP_SEE_OTHER);
        }
        $setting = $settingRepository->getLatestSetting();
        $isAdjustable = false;
        if ($setting !== null) {
            $isAdjustable = $setting->isAdjustable();
        }
        return $this->render('route/new.html.twig', [
            'form' => $form,
            'is_adjustable' => $isAdjustable,
        ]);
    }

    #[Route('/routes/{id:route}/delete', name: 'route_delete', requirements: ['id' => Requirement::POSITIVE_INT], methods: ['POST'])]
    public function delete(Request $request, EntityRoute $route, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $token = (string) $request->getPayload()->get('token');
        if (!$this->isCsrfTokenValid('delete', $token)) {
            $this->addFlash('error', 'Invalid Csrf token.');
            return $this->redirectToRoute('route_view', ['id' => $route->getId()], Response::HTTP_SEE_OTHER);
        }
        $entityManager->remove($route);
        $entityManager->flush();
        $this->addFlash('success', 'Route deleted successfully!');

        return $this->redirectToRoute('route', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/routes/{id:route}/bookmark/add', name: 'route_bookmark_add', requirements: ['id' => Requirement::POSITIVE_INT], methods: ['POST'])]
    public function addBookmark(Request $request, EntityRoute $route, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $token = (string) $request->getPayload()->get('token');
        if (!$this->isCsrfTokenValid('bookmark', $token)) {
            $this->addFlash('error', 'Invalid Csrf token.');
            return $this->redirectToRoute('route_view', ['id' => $route->getId()], Response::HTTP_SEE_OTHER);
        }
        $user = $this->getUser();
        $bookmarks = $user->getBookmarks();
        $bookmarks->add($route);
        $entityManager->persist($user);
        $entityManager->flush();
        $this->addFlash('success', 'Route added bookmarks successfully!');
        return $this->redirectToRoute('route_view', ['id' => $route->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/routes/{id:route}/bookmark/delete', name: 'route_bookmark_delete', requirements: ['id' => Requirement::POSITIVE_INT], methods: ['POST'])]
    public function deleteBookmark(Request $request, EntityRoute $route, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $token = (string) $request->getPayload()->get('token');
        if (!$this->isCsrfTokenValid('bookmark', $token)) {
            $this->addFlash('error', 'Invalid Csrf token.');
            return $this->redirectToRoute('route_view', ['id' => $route->getId()], Response::HTTP_SEE_OTHER);
        }
        $user = $this->getUser();
        $bookmarks = $user->getBookmarks();
        $bookmarks->removeElement($route);
        $entityManager->persist($user);
        $entityManager->flush();
        $this->addFlash('success', 'Route removed from bookmarks successfully!');
        return $this->redirectToRoute('route_view', ['id' => $route->getId()], Response::HTTP_SEE_OTHER);
    }
}