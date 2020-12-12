<?php

namespace App\Form\Type;

use App\Form\DataTransformer\CentimesTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //utiliser la class crée
        if ($options['divide'] === true)
        {
            $builder->addEventSubscriber(new CentimesTransformer());
        }
    }

    public function getParent()
    {
        return NumberType::class; //parent proche du champ a creer

    }
    public function configureOptions(OptionsResolver $resolver)
    {
        //creer les options du champ custom
        $resolver->setDefaults([
            'divide' => true
        ]);
    }
}