<?php


namespace App\Form;


use App\Entity\VideoLink;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoLinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, $options): void
    {
        $builder
            ->add("maxViews", NumberType::class, [
                "required" => false
            ])
            ->add("viewableFor", NumberType::class, [
                "required" => false
            ])
            ->add("viewableUntil", DateType::class, [
                "required" => false
            ])
            ->add("comment", TextType::class, [
                "required" => false
            ])
            ->add("submit", SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VideoLink::class,
        ]);
    }

}