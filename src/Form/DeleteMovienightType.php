<?php

namespace App\Form;

use App\Entity\MovieNight;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DeleteMovienightType
 */
class DeleteMovienightType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder dependency injection
     * @param array                $options for building form
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, ['format' => 'dd MM yyyy', 'label' => false, 'disabled' => true])
            ->add('date', DateType::class, ['format' => 'dd MM yyyy', 'label' => false, 'disabled' => true])
            ->add('time', TimeType::class, ['label' => false, 'disabled' => true])
            ->add('location', TextType::class, ['label' => false, 'disabled' => true])
        ;
    }

    /**
     * @param OptionsResolver $resolver dependency injection
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MovieNight::class,
            'disabled' => true,
        ]);
    }
}
