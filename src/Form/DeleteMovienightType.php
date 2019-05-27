<?php

namespace App\Form;

use App\Entity\MovieNight;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteMovienightType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, ['format' => 'dd MM yyyy', 'label' => false, 'disabled' => true])
            ->add('date', DateType::class, ['format' => 'dd MM yyyy', 'label' => false, 'disabled' => true])
            ->add('time', TimeType::class, ['label' => false, 'disabled' => true])
            ->add('location', TextType::class, ['label' => false, 'disabled' => true])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MovieNight::class,
            'disabled' => true,
        ]);
    }
}
