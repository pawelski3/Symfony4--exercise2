<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;


class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('category',EntityType::class,[
                'class' => 'App\Entity\Category',
                'placeholder'=>"select a category",
                
                "mapped" =>false
            ])
        ;
        
        $builder->get('category')->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event)
        {
            $form=$event->getForm();
            //dump($form->getData());
            $form->getParent()->add('sub_category',EntityType::class,[
                'class'=>'App\Entity\SubCategory',
                'placeholder'=>"Please select a sub category",
               
                "choices"=> $form->getData()->getSubCategories()
            ] );
        }
        );
        
        $builder->addEventListener(
                FormEvents::POST_SET_DATA,
                function(FormEvent $event){
                 $form=$event->getForm();  
                 $data=$event->getData();
                 $sub_category=$data->getSubCategory();
            
                 if($sub_category){
                     $form->get('category')->setData($sub_category->getCategory());
                     $form->add('sub_category',EntityType::class,[
                         'class'=>"App\Entity\SubCategory",
                         'placeholder'=>"Please select sub category",
                         
                         "choices"=>$sub_category->getCategory()->getSubCategories()
                     ]);
                 }else{
                     $form->add('sub_category',EntityType::class,[
                         'class'=>"App\Entity\SubCategory",
                         'placeholder'=>"Please select sub category",
                         
                         "choices"=>[]
                         ]);
                 }
            
                }
                
                
                );
        
        
        
        
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
