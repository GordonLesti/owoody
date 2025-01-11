<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Form\DataTransformer\JsonTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class HoldSetupType extends AbstractType
{
    public function __construct(
        private readonly JsonTransformer $jsonTransformer
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addViewTransformer($this->jsonTransformer);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['rows'] = $options['rows'] ?? 0;
        $view->vars['columns'] = $options['columns'] ?? 0;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'rows' => 0,
            'columns' => 0,
            'compound' => false,
        ]);
        $resolver->setAllowedTypes('rows', 'int');
        $resolver->setAllowedTypes('columns', 'int');
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}