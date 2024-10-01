<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class ArticleForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'required' => true,
                    'constraints' => array(
                        new NotBlank(),
                        new NotNull()
                    ),
                    'attr' => [
                        'placeholder' => 'Title'
                    ]
                ]
            )
            ->add(
                'content',
                TextType::class,
                [
                    'required' => true,
                    'constraints' => array(
                        new NotBlank(),
                        new NotNull()
                    ),
                    'attr' => [
                        'placeholder' => 'Content'
                    ]
                ]
            )
            ->add('image', UrlType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Image'
                ]
            ])
            ->add(
                'price',
                NumberType::class,
                [
                    'required' => true,
                    'constraints' => array(
                        new NotBlank(),
                        new NotNull()
                    ),
                    'attr' => [
                        'placeholder' => 'Price'
                    ]
                ]
            )
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'required' => true,
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
