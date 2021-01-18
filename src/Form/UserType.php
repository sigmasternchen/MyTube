<?php


namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, $options): void
    {
        $builder
            ->add("name", TextType::class)
            ->add("email", EmailType::class)
            ->add("roles", ChoiceType::class, [
                "choices" => [
                    "Admin" => User::ROLE_ADMIN
                ],
                "multiple" => true,
                "expanded" => true,
            ])
            ->add("newPassword", PasswordType::class, [
                "always_empty" => true,
                "required" => !$options["password_optional"]
            ])
            ->add("submit", SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "data_class" => User::class,
            "password_optional" => false
        ]);
    }

}