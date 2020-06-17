<?php

namespace App\Form;

use App\Entity\Realisation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class RealisationType
 * @package App\Form
 */
class RealisationEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, [
                "required" => true,
                'attr' => ['placeholder' => 'Titre...', 'class' => 'form-control']
            ])
            ->add('description', TextType::class, [
                "required" => true,
                'attr' => ['placeholder' => 'Description...', 'class' => 'form-control']
            ])
            ->add('url', TextType::class, [
                "required" => true,
                'attr' => ['placeholder' => 'Url...', 'class' => 'form-control']
            ]);
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Realisation::class,
        ]);
    }
}
