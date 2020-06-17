<?php

namespace App\Filter;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\ChoiceFilterType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RealisationFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextFilterType::class, [
                "attr" => ["placeholder" => "Titre..."]
            ])
            ->add('description', TextFilterType::class, [
                "attr" => ["placeholder" => "Description..."]
            ])
            ->add('url', TextFilterType::class, [
                "attr" => ["placeholder" => "Url..."]
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'user_filter';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}