<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Setting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('rows', IntegerType::class, [
            'label' => 'Rows',
            'mapped' => true,
            'required' => true,
            'help' => 'The number of rows on the board.',
        ])->add('columns', IntegerType::class, [
            'label' => 'Columns',
            'mapped' => true,
            'required' => true,
            'help' => 'The number of columns on the board.',
        ])->add('is_symmetric', CheckboxType::class, [
            'label' => 'Symmetric',
            'mapped' => true,
            'required' => true,
            'help' => 'Is the hold layout of the board symmetric.',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Setting::class,
        ]);
    }
}