<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => array(
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Title'
                )
            ])
            ->add('body', CKEditorType::class, [
                'attr' => array(
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Content Body'
                )
            ])
            -> add('category', ChoiceType::class, [
                'attr' => array(
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Category'
                ),
                'choices' => [
                    'Play of the week' => 'Play of the week',
                    'World records' => 'World records',
                    'Funny stuff' => 'Funny stuff'
                ]
            ])
            -> add('thumbnailPath', FileType::class, array(
                'required' => false,
                'mapped' => false,
                'attr' => array(
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Thumbnail'
                )
            ));
    }
    //->add('user')

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
