<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Setting;
use App\Form\SettingType;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SettingController extends AbstractController
{
    #[Route('/settings', name: 'setting', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        SettingRepository $settingRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $setting = new Setting();
        $parent = $settingRepository->getLatestSetting();
        if ($parent !== null) {
            $setting->setParent($parent);
        }

        $form = $this->createForm(SettingType::class, $setting);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($setting);
                $entityManager->flush();
            }
        } elseif ($parent !== null) {
            $form = $this->createForm(SettingType::class, $parent);
        }

        return $this->render('setting/index.html.twig', [
            'form' => $form,
        ]);
    }
}