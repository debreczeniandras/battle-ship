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
     * @Assert\Count(min="5", max="5")
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
    
    public static function XChoices()
    {
        return range(1, self::WIDTH);
    }
    
    public static function YChoices()
    {
        return range(1, chr(65 + self::HEIGHT));
    }
}
