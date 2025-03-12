<?php

namespace App\Form;

use App\Entity\Chapter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChapterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du chapitre',
                'attr' => [
                    'placeholder' => 'Entrez le titre du chapitre',
                    'aria-required' => 'true',
                    'aria-describedby' => 'chapter-title-help',
                ],
                'label_attr' => [
                    'id' => 'label-chapter-title',
                    'class' => 'required-label',
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => [
                    'placeholder' => 'Rédigez votre chapitre ici...',
                    'rows' => 15,
                    'aria-required' => 'true',
                    'aria-describedby' => 'chapter-content-help',
                ],
                'label_attr' => [
                    'id' => 'label-chapter-content',
                    'class' => 'required-label',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chapter::class,
        ]);
    }
}