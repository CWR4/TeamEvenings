<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DeleteUserType
 */
class DeleteUserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder dependency injection
     * @param array                $options for building form
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
    }

    /**
     * @param OptionsResolver $resolver dependency injection
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
