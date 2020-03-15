<?php
namespace App\Entity;
class Busqueda {
    private $buscar;
    private $tipo;
    private $licitacion;
    private $anio;
    
    function getBuscar() {
        return $this->buscar;
    }

    function getTipo() {
        return $this->tipo;
    }

    function getLicitacion() {
        return $this->licitacion;
    }

    function getAnio() {
        return $this->anio;
    }

    function setBuscar($buscar) {
        $this->buscar = $buscar;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    function setLicitacion($licitacion) {
        $this->licitacion = $licitacion;
    }

    function setAnio($anio) {
        $this->anio = $anio;
    }


}