<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Usuario;
use App\Form\UsuarioType;
use Symfony\Component\HttpFoundation\Session\Session;


class FuncionesController extends AbstractController
{
    //-----------------------------------------------------LOGIN -------------------------------------------------------------------------------------------
    
    
    /**
     * @Route("/asdasdasdsada", name="indexLogin")
     */
    public function index(Request $request)
    {
        $manager=$this->getDoctrine()->getManager();
        $form = $this->createForm(UsuarioType::class,new Usuario());
        $form->handleRequest($request);
        $usuario=$form->getData();
        if ($form->isSubmitted()){
            
            $usuarioRegistrado= $manager->getRepository(Usuario::class)->findBy(['email'=>$usuario->getEmail(),'contrasenia'=>$usuario->getContrasenia()]);
            if ($usuarioRegistrado!=null){
               $fechaActual=  new \DateTime();
               $fechaActual->modify("-3 hours");
               $this->recorrerArreglo($usuarioRegistrado,$fechaActual);//Registra la session y actualiza fecha de ultimo acceso
               $manager->flush();
               $response = $this->forward('App\Controller\ControladorLicitacionesController::index', array( //Llama a index del ControladorLicitaciones
                'Request'  => $request,
            ));
            return $response; 
            }
            else{
                $this->addFlash('error', 'Email o contraseña incorrecta. Intentelo de nuevo.');
                return $this->render('funciones/login.html.twig',['formulario'=>$form->createView()]);
            }
            
        }
        else{
            return $this->render('funciones/login.html.twig',['formulario'=>$form->createView()]);
        }
    }
    
    public function abrirSession($id,$email){
        $session= new Session();
        $session->set('session',$id,$email);
    }
    
    public function recorrerArreglo($arreglo,$fechaActual){
        foreach ($arreglo as $unicoUsuario){
            $unicoUsuario->setUltimoAcceso($fechaActual);
            $this->abrirSession($unicoUsuario->getId(),$unicoUsuario->getEmail());
        }
    }
    
    /**
     * @Route("/asdasdsadas", name="login")
     */
    public function login(Request $request)
    {
        $form = $this->createForm(UsuarioType::class,new Usuario());
        $form->handleRequest($request);
        $usuario=$form->getData();
        if ($form->isSubmitted()){
            return $this->render('funciones/login.html.twig');
        }
        else{
            return $this->render('funciones/login.html.twig');
        }
    }
    
    /**
     * @Route("/funciones/", name="validarLogin")
     */
    public function validarLogin(Request $request)
    {
        $form = $this->createForm(Usuario::class,new Usuario());
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $usuario=$form->getData();
            
        }
        else{
            return $this->render('funciones/index.html.twig',['compras'=>$compras,'formulario'=>$form->createView()]);
        }
    }
    
}
