<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content', TextareaType::class)
            // file input, not mapped to the entity directly
            ->add('pictureFile', FileType::class, (function () {
                $options = [
                    'label' => 'Image (jpg, png)',
                    'mapped' => false,
                    'required' => false,
                ];

                $constraints = [];
                // only check mimeTypes if fileinfo extension is available
                if (extension_loaded('fileinfo') || function_exists('finfo_open')) {
                    $constraints[] = new File(maxSize: '5M', mimeTypes: [
                        'image/jpeg',
                        'image/png',
                    ]);
                } else {
                    // fallback: only enforce max size when mime guessing is unavailable
                    $constraints[] = new File(maxSize: '5M');
                }

                $options['constraints'] = $constraints;

                return $options;
            })())
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name', // ⚠ Assure-toi que Category a un champ "name"
                'placeholder' => 'Choisir une catégorie',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
