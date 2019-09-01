<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Pet;
use App\Validation\StatusConstraint;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PetType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('status', TextType::class, [
                'constraints' => [
                    new StatusConstraint()
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_value' => 'id',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('photoUrls', CollectionType::class, [
                'allow_add' => true
            ])
            ->add('tags', CollectionType::class, [
                'allow_add' => true
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'data_class' => Pet::class,
            'allow_extra_fields' => true,
        ]);
    }
}