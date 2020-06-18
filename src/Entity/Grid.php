<?php

namespace App\Entity;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Serializer\Annotation\Groups;

class Grid
{
    const WIDTH = 8;
    const HEIGHT = 8;
    
    /**
     * The placement of the Ships.
     *
     * @var Ship[]
     *
     * @Groups({"place"})
     */
    private $ships;
    
    /**
     * @var Shot[]
     */
    private $shots;
    
    public function __construct()
    {
        $this->ships = new ParameterBag();
        $this->shots = new ParameterBag();
    }
    
    public function addShip(Ship $ship)
    {
        $this->ships->set($ship->getId(), $ship);
    }
    
    public function addShot(Shot $shot)
    {
        $this->shots->add([$shot]);
    }
}
