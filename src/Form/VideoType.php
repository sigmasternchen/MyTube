<?php


namespace App\Form;


use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, $options): void
    {
        $builder
            ->add("name", TextType::class)
            ->add("description", TextareaType::class)
            ->add("file", FileType::class, [
                "label" => "Video File",
                "mapped" => false,
                "required" => true,
                "constraints" => [
                    new File([
                        "maxSize" => "1024Mi",
                        "maxSizeMessage" => "Yo, the file is too thigh. ({{ limit }} {{ suffix }})",
                        "mimeTypes" => [
                            "video/mp4",
                            "video/H264",
                            "video/H265",
                            "video/3gpp",
                            "video/quicktime",
                            "video/mpv"
                        ],
                        "mimeTypesMessage" => "Video type not supported."
                    ])
                ]
            ])
            ->add("submit", SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }

}