<?php

namespace App\Form;

use App\Entity\MovieNight;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MovieNightType
 */
class MovieNightType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder dependency injection
     * @param array                $options for building form
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'dateAndTime',
                DateTimeType::class,
                [
                    'placeholder' => [
                        'year' => 'Jahr',
                        'month' => 'Monat',
                        'day' => 'Tag',
                        'hour' => 'Std',
                        'minute' => 'Min',
                    ],
                    'label' => false,
                    'years' => range(date('Y'), date('Y')+4),
                    'minutes' => [0, 15, 30, 45],
                    'date_format' => 'dd MM yyyy',
                    'html5' => false,
                ]
            )
            ->add('location', TextType::class, ['empty_data' => 'K56 5.OG', 'data' => 'K56 5.OG', 'label' => false, 'attr' => ['style' => 'width: 200px', 'class' => 'text-center']])
        ;
    }

    /**
     * @param OptionsResolver $resolver dependency injection
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MovieNight::class,
        ]);
    }
}
