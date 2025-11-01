<?php

namespace App\Form;

use App\Entity\AcademicHistory;
use App\Entity\Student;
use App\Entity\Semester;
use App\Entity\Subject;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class AcademicHistoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('grade', NumberType::class, [
                'label' => 'Calificación',
                'scale' => 2,
            ])
            ->add('status', TextType::class, [
                'label' => 'Estado',
            ])
            ->add('student', EntityType::class, [
                'class' => Student::class,
                'choice_label' => 'name',
                'label' => 'Estudiante',
            ])
            ->add('semester', EntityType::class, [
                'class' => Semester::class,
                'choice_label' => 'name',
                'label' => 'Semestre',
            ])
            ->add('subject', EntityType::class, [
                'class' => Subject::class,
                'choice_label' => 'name',
                'label' => 'Materia',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AcademicHistory::class,
        ]);
    }
}
