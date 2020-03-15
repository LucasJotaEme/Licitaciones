<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SistemasController extends AbstractController
{
    /**
     * @Route("/user/sistemas", name="sistemas")
     */
    public function sistemas()
    {
        // Valida al usuario que esté logeado.
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); 
        //Llama al usuario
        $usuario = $this->getUser();
        
        
        //Se obtiene los sistemas del usuario, y se lo pasa al .twig.
        
        return $this->render('sistemas/index.html.twig', [
            'controller_name' => 'SistemasController','sistemas' => $usuario->getSistemas(),'ultimasConexiones' => $this->getUltimasConexiones(), 'tamanio' => count($usuario->getSistemas())
        ]);
    }
    
    public function getUltimasConexiones(){
        $entityManager = $this->getDoctrine()->getManager();
        $query = $entityManager->createQuery(
            "SELECT u
            FROM App\Entity\User u
            ORDER BY u.ultimoAcceso DESC
            "
        );
        $query->setMaxResults(5);

        // returns an array of Product objects
        return $query->getResult();
    }
}
