<?php

namespace App\Form;

use App\Entity\Semester;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SemesterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nombre del Semestre',
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Fecha de Inicio',
                'widget' => 'single_text',
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Fecha de Fin',
                'widget' => 'single_text',
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => '¿Está activo?',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Semester::class,
        ]);
    }
}
