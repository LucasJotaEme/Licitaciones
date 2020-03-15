<?php
namespace App\Entity;
class BusquedaUsuario {
    private $buscar;

    function getBuscar() {
        return $this->buscar;
    }

    function setBuscar($buscar) {
        $this->buscar = $buscar;
    }

}