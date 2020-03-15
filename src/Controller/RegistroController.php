<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistroController extends AbstractController
{
    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        //Vacío, nunca se va a ejecutar nada acá, simplemente cierra sesión. La configuración está en Security.yml
    }
    
    /**
     * @Route("/admin/registro", name="registro")
     */
    public function registro(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $usuario = $form->getData();
            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setUltimoAcceso($this->getFechActual());
            $user->setRoles([$usuario->getRolForm()]);
            $user->setEstado("Alta");
            foreach ($usuario->getSistemas() as $sistema){
                $user->addSistema($sistema);
                $sistema->addUsuario($user);
            }
            
            // 4) save the User!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user
            $this->addFlash('correcto', 'Se registró correctamente el usuario.');
            return $this->redirectToRoute('registro');
        }

        return $this->render(
            'registro/index.html.twig',
            array('form' => $form->createView())
        );
    }
    
    public function getFechActual(){
        $fechaActual=  new \DateTime();
        
        //Se le resta 3 horas a la fecha para que sea correcta a la actual. Desconozco el motivo
        $fechaActual->modify("-3 hours");
        
        return $fechaActual;
    }
}
