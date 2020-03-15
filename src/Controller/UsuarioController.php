<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\EditUserType;
use App\Form\ModificarUserType;

use App\Form\BusquedaUsuarioType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Entity\BusquedaUsuario;

class UsuarioController extends AbstractController
{
    /**
     * @Route("/user/editar", name="editarPerfil")
     */
    public function editarUsuario(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        //Llama a módulo de usuario logeado.
        $usuario = $this->getUsuarioLogeado();
        // Se crea el formulario.
        $form = $this->createForm(EditUserType::class, $usuario);
        
        
        $form->handleRequest($request);
        $contraseniaActual= $usuario->getPassword();
        
        $usuario = $form->getData();
        
        if ($form->isSubmitted() && $form->isValid() && $this->validarUsuario($usuario)) {

            //Se hace las modificaciones del usuario.
            $entityManager = $this->getDoctrine()->getManager();
            $password = $passwordEncoder->encodePassword($usuario, $usuario->getRepetirPassword());
            $usuario->setPassword($password);
            $usuario->setRoles([$usuario->getRolForm()]);
            $usuario->setRepetirPassword(0);
            
            //Se lo actualiza en la DB
            $entityManager->flush();
            
            $this->addFlash('correcto', 'Se actualizó correctamente su perfil');
            
            //Se redirecciona a inicio.
            $response = $this->forward('App\Controller\ControladorLicitacionesController::index', array(
                'Request'  => $request
            ));
            return $response;
        
        }else{
            return $this->render('usuario/index.html.twig', [
            'controller_name' => 'UsuarioController','form' => $form->createView()
            ]);
        }
    }
    
    /**
     * @Route("/admin/buscar", name="buscarUsuarios")
     */
    public function buscarUsuario(Request $request){
        
        $form = $this->createForm(BusquedaUsuarioType::class,new BusquedaUsuario());
        
        $form->handleRequest($request);
        
        $busqueda=$form->getData();
        $entityManager = $this->getDoctrine()->getManager();
        
        if ($form->isSubmitted()){
            return $this->render('usuario/buscar.html.twig',['usuarios'=>$this->getResultadoBusqueda($busqueda->getBuscar()),'form'=>$form->createView()]);
        }
        else{
            //Retorna a buscar.html los usuarios.
            return $this->render('usuario/buscar.html.twig', [
                'controller_name' => 'UsuarioController','usuarios' => $entityManager->getRepository(User::class)->findAll(),'form'=>$form->createView()
                ]);
        }
    }
    
    private function getResultadoBusqueda($busquedaMail){
        $entityManager = $this->getDoctrine()->getManager();
        $query = $entityManager->createQuery(
            "SELECT u
            FROM App\Entity\User u
            WHERE u.email LIKE :email
            ORDER BY u.email ASC
            "
        )->setParameter('email','%'. $busquedaMail . '%');

        // returns an array of Product objects
        return $query->getResult();
    }
    
    /**
     * @Route("/admin/modificar/{id}", name="modificarUsuario")
     */
    public function modificarUsuario(Request $request,$id,UserPasswordEncoderInterface $passwordEncoder){
        $entityManager = $this->getDoctrine()->getManager();
        
        //Tomo el usuario que se eligió
        $usuario= $entityManager->getRepository(User::class)->find($id);
                    
        //Se crea el form
        $form = $this->createForm(ModificarUserType::class, $usuario);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
                
            //Se hace las modificaciones del usuario.
            $usuario = $form->getData();
            $usuario->setRoles([$usuario->getRolForm()]);
            
            foreach ($usuario->getSistemas() as $sistema){
                $usuario->updateSistemas($sistema);
                $sistema->updateUsuarios($usuario);
            }
            
            //Se lo actualiza en la DB
            $entityManager->flush();
            
            $this->addFlash('correcto', 'Se actualizó correctamente su perfil');
            
            //Se redirecciona a inicio.
            return $this->buscarUsuario($request);
        
        }else{
            //Retorna a modificar.html el usuario elegido.
            return $this->render('usuario/index.html.twig', [
            'controller_name' => 'UsuarioController','form' => $form->createView(),'sistemasAnteriores' => $usuario->getSistemas()
            ]);
        }
    }

    
    /**
     * @Route("/admin/baja/{id}", name="bajaUsuario")
     */
    public function bajaUsuario(Request $request,$id){
        
        $entityManager = $this->getDoctrine()->getManager();
        
        //Tomo el usuario que se eligió
        $usuario= $entityManager->getRepository(User::class)->find($id);
            
        //Se cambia a Baja
        $usuario->setEstado("Baja");

        //Se lo actualiza en la DB
        $entityManager->flush();

        //Mensaje
        $this->addFlash('correcto', 'Se dió de baja el usuario');    
            
        return $this->buscarUsuario($request);
    }
    
    /**
     * @Route("/admin/alta/{id}", name="altaUsuario")
     */
    public function altaUsuario(Request $request,$id){
        
        $entityManager = $this->getDoctrine()->getManager();
        
        //Tomo el usuario que se eligió
        $usuario= $entityManager->getRepository(User::class)->find($id);
            
        //Se cambia a Baja
        $usuario->setEstado("Alta");

        //Se lo actualiza en la DB
        $entityManager->flush();

        //Mensaje
        $this->addFlash('correcto', 'Se dió de alta el usuario');    
            
        return $this->buscarUsuario($request);
    }
    
    
    
    
    /**
     * @Route("/admin/eliminar/{id}", name="eliminarUsuario")
     */
    public function eliminarUsuario($id){
        $entityManager = $this->getDoctrine()->getManager();
        
        //Obtengo el usuario seleccionado para eliminarlo directamente.
        
        $entityManager->remove($entityManager->getRepository(User::class)->find($id));
        $entityManager->flush();
        
        //Retorna a buscar.html los usuarios.
        return $this->buscarUsuario();
    }
    
    private function validarUsuario($usuario){
        
        //Verifico que la contraseña nueva sean iguales.
        if ($usuario->getPassword()!=$usuario->getRepetirPassword()){
            $this->addFlash('error', 'Las contraseñas no coinciden.');
            return false;
        }
        return true;
    }
    
    private function getUsuarioLogeado(){
        
        // Valida al usuario que esté logeado.
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        //Llama al usuario
        return $this->getUser();
    }
}