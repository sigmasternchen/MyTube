<?php


namespace App\Form;


use App\Entity\VideoLink;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoLinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, $options): void
    {
        $builder
            ->add("submit", SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VideoLink::class,
        ]);
    }

}