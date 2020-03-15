<?php

namespace App\Controller;

use App\Entity\Compra;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony \ Component \ HttpFoundation \ RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Busqueda;
use App\Entity\Documento;
use App\Form\BusquedaType;
use App\Form\CompraType;

class ControladorLicitacionesController extends AbstractController
{
    
    /**
     * @Route("/user/redireccion", name="redireccion")
     */
    public function redireccion()
    {
        $redireccion = new RedirectResponse('/');
        $redireccion->setTargetUrl('http://localhost/EjemploDocumento/public/index.php/');
        return $redireccion;
    }
    
    /**
     * @Route("/user/inicio", name="indexLicitaciones")
     */
    public function index(Request $request)
    {
        $form = $this->createForm(BusquedaType::class,new Busqueda());
        $form->handleRequest($request);
        $busqueda=$form->getData();
        $compras= $this->validarCierre();
        if ($form->isSubmitted()){
            return $this->render('controlador_licitaciones/index.html.twig',['compras'=>$this->buscar($busqueda),'formulario'=>$form->createView()]);
        }
        else{
            return $this->render('controlador_licitaciones/index.html.twig',['compras'=>$compras,'formulario'=>$form->createView()]);
        }
        
    }
    
    // Valida el cierre de la compra, teniendo en cuenta la fecha.
    
    public function validarCierre(){
        $fechaActual=  new \DateTime();
        $fechaActual->modify("-3 hours");
        $manager=$this->getDoctrine()->getManager();
        
        //Obtiene las ultimas 100 compras.
        $compras= $manager->getRepository(Compra::class)->findBy(array(),
        array('id' => 'DESC'),100);
        
        //Recorre las compras
        foreach ($compras as $compra){
            if ($fechaActual>$compra->getFechaCierre()) {
                $this->cerrarCompra($compra->getId());
                $compra->setEstado("Cerrado");
            }
            else{
                $interval = $fechaActual->diff($compra->getFechaCierre());
                if ($interval->days <='4'){
                    $compra->setEstado("Por cerrar");
                }
                
            }
        }
        return $compras;
            
    }
    
    public function buscar($busqueda){
        //Se hace ciertos IF necesarios para buscar por año, por tipo, año y tipo tambien, o ninguno de los 2.

        if ($busqueda->getTipo() == 2 && $busqueda->getAnio() != 0) { // Consulta a AÑO
            
            return $this->getConsultaAnio($busqueda);
            
        }else if ($busqueda->getTipo() == 2 && $busqueda->getAnio() == 0){//Consulta a NINGUNO
            
            return $this->getConsultaNinguno($busqueda);
            
        }else if ($busqueda->getTipo() != 2 && $busqueda->getAnio() == 0){//Consulta a TIPO
            
            return $this->getConsultaTipo($busqueda);
            
        }else{
            
            return $this->getConsultaTotal($busqueda);
        }
    }
    
    private function getConsultaTotal($busqueda){
        $manager=$this->getDoctrine()->getManager();
        
        $query = $manager->createQuery(
        "SELECT c
        FROM App\Entity\Compra c
        WHERE c.nombre LIKE :nombre AND c.tipo=:tipo AND c.estado LIKE :estado AND c.anio=:anio
        ORDER BY c.id ASC
        "
        )->setParameter('nombre','%'. $busqueda->getBuscar().'%')
         ->setParameter('tipo',$busqueda->getTipo())
         ->setParameter('estado','%'. $busqueda->getLicitacion().'%')
         ->setParameter('anio',$busqueda->getAnio());
        
        //Límite de resultados..
        $query->setMaxResults(100);
        
        //Retorna busqueda de la compra..
        return $query->getResult();
    }
    
    private function getConsultaAnio($busqueda){
        $manager=$this->getDoctrine()->getManager();
        
        $query = $manager->createQuery(
        "SELECT c
        FROM App\Entity\Compra c
        WHERE c.nombre LIKE :nombre AND c.estado LIKE :estado AND c.anio=:anio
        ORDER BY c.id ASC
        "
        )->setParameter('nombre','%'. $busqueda->getBuscar().'%')
         ->setParameter('estado','%'. $busqueda->getLicitacion().'%')
         ->setParameter('anio',$busqueda->getAnio());
        
        //Límite de resultados..
        $query->setMaxResults(100);
        
        //Retorna busqueda de la compra..
        return $query->getResult();
    }
    
    private function getConsultaNinguno($busqueda){
        $manager=$this->getDoctrine()->getManager();
        
        $query = $manager->createQuery(
        "SELECT c
        FROM App\Entity\Compra c
        WHERE c.nombre LIKE :nombre AND c.estado LIKE :estado
        ORDER BY c.id ASC
        "
        )->setParameter('nombre','%'. $busqueda->getBuscar().'%')
         ->setParameter('estado','%'. $busqueda->getLicitacion().'%');
        
        //Límite de resultados..
        $query->setMaxResults(100);
        
        //Retorna busqueda de la compra..
        return $query->getResult();
    }
    
    private function getConsultaTipo($busqueda){
        $manager=$this->getDoctrine()->getManager();
        
        $query = $manager->createQuery(
        "SELECT c
        FROM App\Entity\Compra c
        WHERE c.nombre LIKE :nombre AND c.tipo=:tipo AND c.estado LIKE :estado 
        ORDER BY c.id ASC
        "
        )->setParameter('nombre','%'. $busqueda->getBuscar().'%')
         ->setParameter('tipo',$busqueda->getTipo())
         ->setParameter('estado','%'. $busqueda->getLicitacion().'%');
        
        //Límite de resultados..
        $query->setMaxResults(100);
        
        //Retorna busqueda de la compra..
        return $query->getResult();
    }
    
    /**
     * @Route("/user/crearCompra/", name="crearCompra")
     */
    public function crearCompra(Request $request)
    {
        $fechaActual=  new \DateTime();
        $fechaCierre=  new \DateTime();
        $fechaActual->modify("-3 hours");
        $fechaEntera = strtotime($fechaActual->format('Y-m-d H:i:s'));
        $anioActual= date("Y", $fechaEntera);
        $fechaCierre->modify("-3 hours");
        $form = $this->createForm(\App\Form\CompraType::class,new Compra());
        $form->handleRequest($request);
        $compra = $form->getData(); //Almacena los datos en compra de los ingresados en el formulario.
        if ($form->isSubmitted() && $form->isValid() && $this->validarDatos($compra)) {
            $manager = $this->getDoctrine()->getManager();
            $archivos = $compra->getDocumento();
            $compra->setEstado("Activo");
            $compra->setAnio($anioActual);
            $compra->setIdUsuario($this->getUser()->getId());
            $manager->persist($compra);
            $manager->flush();
            if (count($archivos)<10){
                foreach ($archivos as $archivo) {
                    $documento = new Documento();
                    $extensionArchivo=$archivo->guessExtension();
                    $nombreArchivo= time().".".$extensionArchivo;
                    $archivo->move("uploads",$nombreArchivo);
                    $documento->setDocumento($nombreArchivo);
                    $documento->setExtension($extensionArchivo);
                    $documento->setCompraId($compra->getId());

                    $manager->persist($documento);
                    $manager->flush();
                }
                $this->addFlash('correcto', 'Solicitud de compra creada correctamente!');
                return $this->index($request);
            }else{
                $this->addFlash('error', 'ERROR: No se pueden agregar más de 10 documentos.');
                return $this->render('controlador_licitaciones/crearCompra.html.twig',
                array('formulario'=>$form->createView()));
            }
            
        } else {
            $fechaActual->modify("+10 minutes");
            $fechaCierre->modify("+1 months 10 minutes");
            $compra->setFechaApertura($fechaActual);
            $compra->setFechaCierre($fechaCierre);
            $form = $this->createForm(CompraType::class,$compra);
            return $this->render('controlador_licitaciones/crearCompra.html.twig',
            array('formulario'=>$form->createView()));
        }
    }
    
    public function validarDatos($compra){
        $fechaActual=  new \DateTime();
        $fechaActual->modify("-3 hours");
        //$this->addFlash('error', 'La fecha de cierre está antes de fecha de apertura.'. $compra->getFechaApertura()->format('Y-m-d H:i:s'). 'comparando con: '.$fechaActual);
        if ($fechaActual>$compra->getFechaApertura()) {
            $this->addFlash('error', 'La fecha de apertura ya pasó.' .$fechaActual->format('d-m-Y H:i:s'));
            return false;
        } else if ($fechaActual>$compra->getFechaCierre())  {
            $this->addFlash('error', 'Fecha de cierre ya pasó.');
            return false;
        } else if ($compra->getFechaCierre()<$compra->getFechaApertura()) {
            $this->addFlash('error', 'La fecha de cierre está antes de fecha de apertura.');
            return false;
        }else if ($compra->getDocumento()!=null && $compra->getDocumento()== "application/pdf" || $compra->getDocumento()== "application/zip"){
            $this->addFlash('error', 'Sólo se acepta documentos .PDF y .ZIP.');
            return false;
        }else {
            return true;
        }
    }
    
    /**
     * @Route("/user/verDocumentos/{id}/", name="verDocumentos")
     */
    public function verDocumentos(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $documentos= $em->getRepository(Documento::class)->findBy(['compraId'=>$id]);
        return $this->render('controlador_licitaciones/verDocumentos.html.twig',
            array('documentos'=>$documentos));
    }
    
    /**
     * @Route("/user/verMisLicitaciones/", name="misLicitaciones")
     */
    public function verMisLicitaciones(Request $request){
        $form = $this->createForm(BusquedaType::class,new Busqueda());
        $form->handleRequest($request);
        $busqueda=$form->getData();
        $em = $this->getDoctrine()->getManager();
        $compras= $em->getRepository(Compra::class)->findBy(['idUsuario'=>$this->getUser()->getId()]);
        
        if ($form->isSubmitted()){
            return $this->render('controlador_licitaciones/index.html.twig',['compras'=>$this->buscar($busqueda),'formulario'=>$form->createView()]);
        }
        else{
            return $this->render('controlador_licitaciones/index.html.twig',['compras'=>$compras,'formulario'=>$form->createView()]);
        }
        
    }
    
    /**
     * @Route("/user/modificarCompra/{id}/", name="modificarCompra")
     */
    public function modificarCompra(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $compra = $em->getRepository(Compra::class)->find($id); 
        $form = $this->createForm(CompraType::class,$compra);
        $archivosPrevios = $em->getRepository(Documento::class)->findBy(['compraId'=>$id]);
        $form->handleRequest($request);
        if ($form->isSubmitted() &&  $form->isValid() && $this->validarDatos($compra)){
            $compra = $form->getData();
            
            $manager = $this->getDoctrine()->getManager();
            $archivos = $compra->getDocumento();
            $manager->flush();
            if (count($archivos)<10){
                foreach ($archivos as $archivo) {
                    $documento = new Documento();
                    $extensionArchivo=$archivo->guessExtension();
                    $nombreArchivo= time().".".$extensionArchivo;
                    $archivo->move("uploads",$nombreArchivo);

                    $documento->setDocumento($nombreArchivo);
                    $documento->setExtension($extensionArchivo);
                    $documento->setCompraId($compra->getId());
                    $manager->persist($documento);
                    $manager->flush();
                }
                $this->addFlash('correcto', 'Se ha modificado correctamente');
                return $this->index($request);          
            }else{
                $this->addFlash('error', 'ERROR: No se pueden agregar más de 10 documentos.');
                return $this->render('controlador_licitaciones/modificarCompra.html.twig',
                array('formulario'=>$form->createView(),'cantidadArchivosSubidos'=> count($archivosPrevios)));         
            }
        }
        else{
            
            return $this->render('controlador_licitaciones/modificarCompra.html.twig',
            array('formulario'=>$form->createView(),'cantidadArchivosSubidos'=> count($archivosPrevios)));
        }
    }
    
    
    
    /**
     * @Route("/user/eliminarCompra/{id}/", name="eliminarCompra")
     */
    public function eliminarCompra(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $compra = $em->getRepository(Compra::class)->find($id); 
        $form = $this->createForm(CompraType::class,$compra);
        $form->handleRequest($request);
            $compra = $form->getData();
            $compra->setEstado("Baja");
            $em->flush();      
            $this->addFlash('correcto', 'Se ha dado de baja correctamente');      
        return $this->index($request);
    }
    
    /**
     * @Route("/user/eliminarTodasBajas/", name="eliminarBajas")
     */
    public function eliminarTodasBajas(Request $request){
        $manager=$this->getDoctrine()->getManager();
        $compras= $manager->getRepository(Compra::class)->findBy(['estado' => 'Baja'],
        array('id' => 'DESC'),100
                );
        foreach ($compras as $compra){
            $manager->remove($compra);
        }
        $manager->flush();
        $this->addFlash('correcto', 'Se eliminaron todas las bajas.');      
        return $this->activarBajas($request);
    }
    
    /**
     * @Route("/user/altaCompra/{id}/", name="altaCompra")
     */
    public function altaCompra(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $compra = $em->getRepository(Compra::class)->find($id); 
        $form = $this->createForm(CompraType::class,$compra);
        $form->handleRequest($request);
            $compra = $form->getData();
            $compra->setEstado("Activo");
            $em->flush();      
            $this->addFlash('warning', 'AVISO: Recuerde modificar la fecha de apertura, como la de ciere.');      
        return $this->modificarCompra($request, $id);
    }
    
    /**
     * @Route("/user/quitarCompra/{id}/", name="quitarCompra")
     */
    public function quitarCompra(Request $request,$id){
        $em = $this->getDoctrine()->getEntityManager();
        $compra = $em->getRepository(Compra::class)->find($id);
        $em->remove($compra);
        $em->flush();
        return $this->index($request);
    }
    
    public function cerrarCompra($id){
        $em = $this->getDoctrine()->getManager();
        $compra = $em->getRepository(Compra::class)->find($id);
        $compra->setEstado("Cerrado");
        $em->flush();      
    }
    
    /**
     * @Route("/user/activarBajas/", name="activarBajas")
     */
    public function activarBajas(Request $request)
    {
        
        $manager=$this->getDoctrine()->getManager();
        $compras= $manager->getRepository(Compra::class)->findBy(['estado' => 'Baja'],
        array('id' => 'DESC'),100
                );
        return $this->render('controlador_licitaciones/activarBajas.html.twig',
            array('compras'=>$compras));
    }
    
    /**
     * @Route("/user/eliminarDocumento/{id}/{idCompra}", name="eliminarDocumento")
     */
    public function eliminarDocumento(Request $request,$id,$idCompra)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $documento = $em->getRepository(Documento::class)->find($id);
        $compra = $em->getRepository(Compra::class)->find($idCompra);
        $em->remove($documento);
        $em->flush();
        $documentos= $em->getRepository(Documento::class)->findBy(['compraId'=>$idCompra]);
        $this->addFlash('correcto', 'Se ha borrado corectamente el documento.'); 
        if (count($documentos) === 0){
            $compra->setDocumentoNulo(array());
            $em->flush();
            return $this->index($request);
        } else {
            return $this->verDocumentos($request,$idCompra);
        }
        
    }
    
    /**
     * @Route("/user/eliminarDocumento/{idCompra}", name="eliminarDocumentos")
     */
    public function eliminarDocumentos(Request $request,$idCompra)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $compra = $em->getRepository(Compra::class)->find($idCompra);
        $documentos= $em->getRepository(Documento::class)->findBy(['compraId'=>$idCompra]);
        $this->addFlash('correcto', 'Se borraron todos los documentos.');
        foreach ($documentos as $documento){
            $em->remove($documento);
        }
        $compra->setDocumentoNulo(array());
        $em->flush();
        return $this->index($request);
        
        
    }
    
    
    
    
}
