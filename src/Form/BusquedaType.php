<?php
namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class BusquedaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        $builder
            ->add('buscar',TextType::class,array("required"=>false))
            ->add('tipo',ChoiceType::class,['choices' =>[
                'Tipo'=>2,
                'Licitación'=>1,
                'Concurso de precios'=>0],
                'attr'=>['class'=>'form-control']
            ])
            ->add('licitacion',ChoiceType::class,['choices' =>[
                'Estado'=>'',
                'Activo'=>"Activo",
                'Cerrado'=>"Baja"]])
            ->add('anio',ChoiceType::class,['choices' =>[
                'Año'=>0,
                '2019'=>2019,
                '2020'=>2020,
                '2021'=>2021,
                '2022'=>2022]])
                
            
            ->add('Buscar',SubmitType::class)
                
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Entity\Busqueda::class,
        ]);
    }
}

