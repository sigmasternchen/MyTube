<?php


namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, $options): void
    {

        if ($options["profile_picture"]) {
            $builder->add("file", FileType::class, [
                "label" => "Profile Picture",
                "mapped" => false,
                "required" => false,
                "constraints" => [
                    new File([
                        "maxSize" => "10Mi",
                        "mimeTypes" => [
                            "image/jpeg",
                            /*"image/gif",
                            "image/png",
                            "image/tiff",
                            "image/webp"*/
                        ]
                    ])
                ]
            ]);
        }

        $builder
            ->add("name", TextType::class)
            ->add("email", EmailType::class);

        if ($options["roles"]) {
            $builder->add("roles", ChoiceType::class, [
                "choices" => [
                    "Admin" => User::ROLE_ADMIN
                ],
                "multiple" => true,
                "expanded" => true,
            ]);
        }

        $builder
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
            "password_optional" => false,
            "roles" => true,
            "profile_picture" => false
        ]);
    }

}