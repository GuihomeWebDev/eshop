<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => [
                    'placeholder' => 'exemple de produit'
                ]
            ])
            ->add('price', MoneyType::class, [
                'label' => ' Prix du produit',
                'divisor' => 100,
                'attr' => [
                    'placeholder' => '50€',
                ],
            ])
            ->add('picture', UrlType::class, [
                'label'=> 'Url de l\'image du produit',
                'attr'=> [
                    'placeholder'=> 'url de l\'image'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label'=> 'description produit',
                "attr"=>[
                    'placeholder'=> 'petite description courte de votre produit'
                ]
            ])
            ->add('category', EntityType::class, [
                'label'=> 'Categorie',
                'placeholder' => '--choisir une catégorie--',
                'class' => Category::class,
                'choice_label'=> function(Category $category){
                    return strtoupper($category->getName());
                }
            ]);
        //Ajouter un evenement au formulaire (pour modifier les champs du formulaire)
        /*$builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event){
            $form = $event->getForm();
            /**
             * @var Product
             */
           /* $product = $event->getData();
            if($product->getId() === null)
            {
                $form->add('category', EntityType::class, [
                    'label'=> 'Categorie',
                    'placeholder' => '--choisir une catégorie--',
                    'class' => Category::class,
                    'choice_label'=> function(Category $category){
                        return strtoupper($category->getName());
                    }
                ]);
            }*/
        /*});*/

        //creer un data transformer sur le formulaire (pour modifier les datas du formulaire)
        /*$builder->get('price')->addModelTransformer(new CallbackTransformer(
            function ($value){
                if ($value !== null){
                return $value / 100;
                }
            },
            function ($value){
                if ($value !== null){
                return $value * 100;
                }
            }
        ));*/
        //ou utiliser la class 'CentimesTransformer' (dans le dossier Form\DataTransformer
/*        $builder-get('price')->addModelTransformer(new CentimesTransformer());*/
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}