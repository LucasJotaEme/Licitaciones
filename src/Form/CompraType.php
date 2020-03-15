<?php

namespace App\Form;
use App\Entity\Compra;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CompraType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        $builder
            ->add('nombre',TextType::class,array('id '=>'textoBuscar', "required"=>true,
                                                 'attr' => array('class'=>'form-control','placeholder'=>'Ingrese nombre de la compra..')
                                            ))
            ->add('tipo',ChoiceType::class,['choices' =>[
                'Licitación'=>1,
                'Concurso de precios'=>0],
                'attr'=>['class'=>'form-control']
            ])
            ->add('visita')
            ->add('plano')
            ->add('consulta',TextareaType::class,['required'=>false,'attr'=>['rows'=>"3",'cols'=>"30",'class'=>'form-control','placeholder'=>'Ingrese su consulta..']])
            ->add('fechaApertura')
            ->add('fechaCierre')
            ->add('documento',FileType::class,['required'=>false,'multiple'=>true])
            ->add('guardar',SubmitType::class)
                
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Compra::class,
        ]);
    }
}
