<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Sistema;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',EmailType::class,array("required"=>true,
                                                 'attr' => array('class'=>'form-control','placeholder'=>'Ingrese email..')
            
                                            ))
            ->add('rolForm',ChoiceType::class,['disabled'=> true, 'choices' =>[
                'Usuario'=>'ROLE_USER',
                'Administrador'=>'ROLE_ADMIN',
                ]
            ])
            ->add('sistemas', EntityType::class,[ 
                'disabled' => true,
                'required'=>false,
                'multiple'=>true,
                'class' =>Sistema::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.nombre', 'ASC');
                },
                'choice_label' => 'nombre',
            ])
            ->add('password', PasswordType::class,array("required"=>true,
                                                 'attr' => array('class'=>'form-control','placeholder'=>'Ingrese la nueva contraseña..')
                                            ))
            ->add('repetirPassword', PasswordType::class,array("required"=>true,
                                             'attr' => array('class'=>'form-control','placeholder'=>'Ingrese nuevamente la contraseña..')
                                        ))
            
            ->add('guardar',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}
