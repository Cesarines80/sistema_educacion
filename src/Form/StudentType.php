<?php

namespace App\Form;

use App\Entity\Student;
use App\Entity\AcademicGrade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nombre',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Correo Electrónico',
            ])
            ->add('phone', TelType::class, [
                'label' => 'Teléfono',
                'required' => false,
            ])
            ->add('birthDate', DateType::class, [
                'label' => 'Fecha de Nacimiento',
                'widget' => 'single_text',
            ])
            ->add('academicGrade', EntityType::class, [
                'class' => AcademicGrade::class,
                'choice_label' => 'name',
                'label' => 'Grado Académico',
                'placeholder' => 'Seleccione un grado académico',
            ])
            ->add('subjects', EntityType::class, [
                'class' => \App\Entity\Subject::class,
                'choice_label' => 'name',
                'label' => 'Materias',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
