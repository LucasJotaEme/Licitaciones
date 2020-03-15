<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompraRepository")
 */
class Compra
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    public function __construct()
    {
        $this->documentos = new ArrayCollection();
    }

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="boolean")
     */
    private $visita;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tipo;

    /**
     * @ORM\Column(type="boolean")
     */
    private $plano;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $consulta;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fechaApertura;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fechaCierre;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $estado;
    
    /**
     * @ORM\Column(type="integer" , nullable=true)
     */
    private $anio;
    
    /**
     * @ORM\Column(type="integer" , nullable=false)
     */
    private $idUsuario;
            
    private $chequeado;

    
    function getChequeado() {
        return $this->chequeado;
    }

    function setChequeado(bool $chequeado) {
        $this->chequeado = $chequeado;
    }
    
    function getIdUsuario() {
        return $this->idUsuario;
    }

    function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario;
    }
   

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $documento = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getVisita(): ?bool
    {
        return $this->visita;
    }

    public function setVisita(bool $visita): self
    {
        $this->visita = $visita;

        return $this;
    }
        
    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getPlano(): ?bool
    {
        return $this->plano;
    }

    public function setPlano(bool $plano): self
    {
        $this->plano = $plano;

        return $this;
    }

    public function getConsulta(): ?string
    {
        return $this->consulta;
    }

    public function setConsulta(?string $consulta): self
    {
        $this->consulta = $consulta;

        return $this;
    }

    public function getFechaApertura(): ?\DateTimeInterface
    {
        return $this->fechaApertura;
    }

    public function setFechaApertura(\DateTimeInterface $fechaApertura): self
    {
        $this->fechaApertura = $fechaApertura;

        return $this;
    }

    public function getFechaCierre(): ?\DateTimeInterface
    {
        return $this->fechaCierre;
    }

    public function setFechaCierre(\DateTimeInterface $fechaCierre): self
    {
        $this->fechaCierre = $fechaCierre;

        return $this;
    }
    function getEstado() {
        return $this->estado;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

    public function getDocumento(): ?array
    {
        return $this->documento;
    }
    
    public function setDocumento(array $documento): self
    {
        $this->documento = $documento;
        return $this;
    }
    
    public function setDocumentoNulo(array $documento)
    {
        $this->documento = null;
        return $this;
    }
    
    function getAnio() {
        return $this->anio;
    }

    function setAnio($anio) {
        $this->anio = $anio;
    }




}
