<?php

namespace App\Form;

use App\Entity\Story;
use App\Enum\StoryEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class StoryType extends AbstractType
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Titre de votre histoire',
                    'aria-required' => 'true',
                    'aria-describedby' => 'title-help',
                ],
                'label_attr' => [
                    'id' => 'label-title',
                    'class' => 'required-label title',
                ],
                'help' => 'Donnez un titre à votre histoire. Maximum 30 caractères.',
                'help_attr' => [
                    'id' => 'title-help',
                    'class' => 'help-text',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un titre pour votre histoire',
                    ]),
                    new Length([
                        'max' => 30,
                        'maxMessage' => 'Le titre ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Description de votre histoire',
                    'rows' => 5,
                    'aria-required' => 'true',
                    'aria-describedby' => 'description-help',
                ],
                'label_attr' => [
                    'id' => 'label-description',
                    'class' => 'required-label title',
                ],
                'help' => 'Décrivez votre histoire en quelques lignes. Maximum 250 caractères.',
                'help_attr' => [
                    'id' => 'description-help',
                    'class' => 'help-text',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une description pour votre histoire',
                    ]),
                    new Length([
                        'max' => 250,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('genre', EnumType::class, [
                'label' => 'Genre',
                'required' => true,
                'class' => StoryEnum::class,
                'choice_label' => fn (StoryEnum $genre) => $genre->getLabel($this->translator),
                'multiple' => true,
                'expanded' => false,
                'attr' => [
                    'aria-required' => 'true',
                    'aria-describedby' => 'genre-help',
                ],
                'label_attr' => [
                    'id' => 'label-genre',
                    'class' => 'required-label title',
                ],
                'help' => 'Sélectionnez un genre pour votre histoire.',
                'help_attr' => [
                    'id' => 'genre-help',
                    'class' => 'help-text',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un genre',
                    ]),
                ]
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image de couverture',
                'mapped' => true,
                'required' => false,
                'attr' => [
                    'aria-describedby' => 'image-help',
                    'class' => 'file-input',
                ],
                'label_attr' => [
                    'id' => 'label-image',
                    'class' => 'title',
                ],
                'help' => 'Formats acceptés: JPEG, PNG, WEBP. Taille maximale: 2Mo.',
                'help_attr' => [
                    'id' => 'image-help',
                    'class' => 'help-text',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG, PNG, WEBP)',
                    ])
                ],
            ])
            ->add('status', CheckboxType::class, [
                'label' => 'Publier cette histoire',
                'required' => false,
                'attr' => [
                    'class' => 'h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2',
                    'aria-describedby' => 'status-help',
                ],
                'label_attr' => [
                    'id' => 'label-status',
                    'class' => 'text-sm font-medium text-gray-700',
                ],
                'help' => 'Cochez cette case pour rendre l\'histoire visible par tous les utilisateurs.',
                'help_attr' => [
                    'id' => 'status-help',
                    'class' => 'help-text',
                ],
            ])
        ;
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        foreach ($view->children as $childView) {
            if (count($childView->vars['errors']) > 0) {
                $childView->vars['attr']['aria-invalid'] = 'true';

                $errorId = $childView->vars['id'] . '-error';
                $describedBy = isset($childView->vars['attr']['aria-describedby'])
                    ? $childView->vars['attr']['aria-describedby'] . ' ' . $errorId
                    : $errorId;
                $childView->vars['attr']['aria-describedby'] = $describedBy;

                $childView->vars['errors']->vars['id'] = $errorId;
                $childView->vars['errors']->vars['attr']['aria-live'] = 'assertive';
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Story::class,
            'attr' => [
                'aria-label' => 'Formulaire de création/modification d\'histoire',
                // 'novalidate' => 'novalidate',
            ],
            'error_bubbling' => false,
        ]);
    }
}