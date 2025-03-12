<?php

namespace App\Form;

use App\Enum\StoryEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class MainStoryType extends AbstractType
{

    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('genre', ChoiceType::class, [
                'label' => 'Genre',
                'required' => false,
                'placeholder' => 'Chercher par genre',
                'choices' => StoryEnum::getChoices($this->translator),
                'attr' => [
                    'class' => 'genre-select',
                    'aria-label' => 'Filtrer par genre',
                ],
                'label_attr' => [
                    'class' => 'sr-only',
                ],
            ])
            ->add('query', SearchType::class, [
                'label' => 'Recherche',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Chercher une histoire...',
                    'class' => 'search-input',
                    'aria-label' => 'Rechercher une histoire',
                ],
                'label_attr' => [
                    'class' => 'sr-only',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => [
                    'class' => 'submit-button btn',
                    'aria-label' => 'Lancer la recherche',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
            'attr' => [
                'aria-label' => 'Formulaire de recherche d\'histoires',
                'role' => 'search',
            ],
        ]);
    }
}