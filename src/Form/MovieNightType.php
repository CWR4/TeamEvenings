<?php

namespace App\Form;

use App\Entity\MovieNight;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieNightType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, ['placeholder' => ['year' => 'Jahr' , 'month' => 'Monat', 'day' => 'Tag'], 'format' => 'dd MM yyyy', 'label' => false, 'years' => range(date('Y'), date('Y')+4)])
            ->add('time', TimeType::class, ['placeholder' => ['hour' => 'Std', 'minute' => 'Min'], 'label' => false, 'minutes' => [0,15,30,45]])
            ->add('location', TextType::class ,['empty_data' => 'K56 5.OG', 'data' => 'K56 5.OG', 'label' => false, 'attr' => ['style' => 'width: 200px', 'class' => 'text-center']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MovieNight::class,
        ]);
    }
}