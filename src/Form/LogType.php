<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Log;
use App\Entity\Route;
use App\Entity\User;
use App\Enum\Fontainebleau;
use App\Form\Type\IsMirroredType;
use App\Form\Type\RatingType;
use BackedEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('user', EntityType::class, [
            'mapped' => true,
            'required' => true,
            'class' => User::class,
            'choice_label' => 'username',
            'disabled' => true,
        ])->add('route', EntityType::class, [
            'mapped' => true,
            'required' => true,
            'class' => Route::class,
            'choice_label' => 'name',
            'disabled' => true,
        ])->add('is_mirrored', IsMirroredType::class, [
            'mapped' => true,
            'required' => true,
            'route_entity' => $options['route_entity'],
        ])->add('angle', ChoiceType::class, [
            'label' => 'Angle',
            'mapped' => true,
            'choices' => array_combine(range(0, 40, 5), range(0, 40, 5)),
            'required' => false,
            'disabled' => $options['data']->getId() === null,
            'invalid_message' => 'The angle is invalid.',
        ])->add('is_success', CheckboxType::class, [
            'label' => 'Success',
            'mapped' => true,
            'required' => true,
        ])->add('grade', EnumType::class, [
            'label' => 'Grade',
            'mapped' => true,
            'required' => true,
            'class' => Fontainebleau::class,
            'choice_label' => static function (BackedEnum $choice): string {
                return (string) $choice->value;
            },
        ])->add('rating', RatingType::class, [
            'label' => 'Rating',
            'mapped' => true,
            'required' => true,
        ])->add('attempts', IntegerType::class, [
            'label' => 'Attempts',
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
            'data_class' => Log::class,
            'route_entity' => null,
        ]);
    }
}