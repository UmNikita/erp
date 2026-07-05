<?php

namespace App\Home\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints as Assert;

class EditPasswordUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Пароли не совпадают',
            'required' => true,
            'first_options' => [
                'label' => 'Пароль',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min:4),
                ],
            ],
            'second_options' => [
                'label' => 'Повторите пароль',
            ],
        ]);
    }
}