<?php

namespace App\Form;

use App\Enum\StoryEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MainStoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $genres = [];
        foreach (StoryEnum::cases() as $genre) {
            $genres[$genre->value] = $genre->name;
        }

        $builder
            ->add('genre', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'placeholder' => 'Chercher par genre',
                'choices' => $genres,
                'attr' => [
                    'class' => 'genre-select'
                ]
            ])
            ->add('query', SearchType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Chercher une histoire...',
                    'class' => 'search-input'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => [
                    'class' => 'submit-button btn'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}