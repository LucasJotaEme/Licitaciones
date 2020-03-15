<?php
namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class BusquedaUsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        $builder
            ->add('buscar',TextType::class,array("required"=>false))            
            ->add('Buscar',SubmitType::class)
                
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Entity\BusquedaUsuario::class,
        ]);
    }
}

