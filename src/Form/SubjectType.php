<?php

namespace App\Form;

use App\Entity\Subject;
use App\Entity\AcademicGrade;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class SubjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nombre de la Materia',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Descripción',
                'required' => false,
            ])
            ->add('credits', IntegerType::class, [
                'label' => 'Créditos',
            ])
            ->add('academicGrade', EntityType::class, [
                'class' => AcademicGrade::class,
                'choice_label' => 'name',
                'label' => 'Grado Académico',
                'placeholder' => 'Seleccione un grado académico',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subject::class,
        ]);
    }
}
