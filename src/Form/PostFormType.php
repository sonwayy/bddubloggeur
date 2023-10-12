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
                    'placeholder' => 'Titre de l\'article'
                )
            ])
            ->add('body', CKEditorType::class, [
                'attr' => array(
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Corps de l\'article'
                )
            ])
            -> add('category', ChoiceType::class, [
                'attr' => array(
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Categorie'
                ),
                'choices' => [
                    'Comparatif' => 'Comparatif',
                    'Retour d’expérience' => 'Retour d’expérience',
                    'Tutoriel' => 'Tutoriel',
                    'Conseil' => 'Conseil',
                    'Classement' => 'Classement',
                    'Actualité' => 'Actualité',
                    'FAQ' => 'FAQ',
                    'Nouveau produit' => 'Nouveau produit',
                    'Biographie' => 'Biographie',
                    'Partage d’expérience' => 'Partage d’expérience',
                    'Partage d\'opinion' => 'Partage d\'opinion',
                ]
            ])
            -> add('thumbnailPath', FileType::class, array(
                'required' => false,
                'mapped' => false,
                'attr' => array(
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Image de l\'article'
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
