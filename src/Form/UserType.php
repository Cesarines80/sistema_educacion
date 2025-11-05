<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isNew = !$options['data'] || !$options['data']->getId();

        $builder
            ->add('email', EmailType::class)
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Administrador' => 'ROLE_ADMIN',
                    'Estudiante' => 'ROLE_STUDENT',
                    'Profesor' => 'ROLE_TEACHER',
                ],
                'multiple' => true,
                'expanded' => true,
                'label' => false,
                'data' => $isNew ? ['ROLE_USER'] : $options['data']->getRoles(),
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => $isNew,
                'mapped' => false,
                'first_options' => ['label' => 'Contraseña'],
                'second_options' => ['label' => 'Repetir Contraseña'],
                'invalid_message' => 'Las contraseñas no coinciden.',
            ])
            ->add('name', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
