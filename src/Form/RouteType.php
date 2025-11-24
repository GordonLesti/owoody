<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Route;
use App\Form\Type\HoldSetupType;
use App\Repository\SettingRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RouteType extends AbstractType
{
    public function __construct(private readonly SettingRepository $settingRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $setting = $this->settingRepository->getLatestSetting();

        $builder->add('hold_setup', HoldSetupType::class, [
            'label' => 'Hold Setup',
            'mapped' => true,
            'required' => true,
            'rows' => $setting !== null ? $setting->getRowCount() : 0,
            'columns' => $setting !== null ? $setting->getColumnCount() : 0,
            'invalid_message' => 'The hold setup is invalid.',
            'route_entity' => $options['route_entity'],
        ])->add('name', TextType::class, [
            'label' => 'Name',
            'mapped' => true,
            'required' => true,
        ])->add('note', TextareaType::class, [
            'label' => 'Note',
            'mapped' => true,
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Route::class,
            'route_entity' => null,
        ]);
    }
}