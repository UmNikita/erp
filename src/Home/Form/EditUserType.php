<?php

namespace App\Home\Form;

use App\Entity\Department;
use App\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(min: 3),
            ],
        ])
        ->add('email', EmailType::class, [
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Email(),
            ],
        ])
        ->add('role', EntityType::class, [
            'class' => Role::class,
            'choice_label' => 'name',
            'placeholder' => '-',
            'required' => false,
        ])
        ->add('department', EntityType::class, [
            'class' => Department::class,
            'choice_label' => 'name',
            'placeholder' => '-',
            'required' => false,
        ]);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'roles' => [],
            'departments' => [],
            'csrf_protection' => true,
        ]);
    }
}