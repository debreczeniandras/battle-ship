<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class Grid
{
    const WIDTH = 8;
    const HEIGHT = 8;
    
    /**
     * The placement of the Ships.
     *
     * @var Ship[]
     *
     * @Serializer\Groups({"place"})
     * @Assert\Count(min="5", max="5")
     */
    private $ships;
    
    /**
     * @var Shot[]
     * @Serializer\Ignore()
     */
    private $shots;
    
    public function __construct()
    {
        $this->ships = new ArrayCollection();
        $this->shots = new ArrayCollection();
    }
    
    public function addShip(Ship $ship)
    {
        $this->ships->add($ship);
    }
    
    public function addShot(Shot $shot)
    {
        $this->shots->add($shot);
    }
    
    public static function XChoices()
    {
        return range(1, self::WIDTH);
    }
    
    public static function YChoices()
    {
        return range(1, chr(65 + self::HEIGHT));
    }
    
    /**
     * @return Ship[]|ArrayCollection
     */
    public function getShips()
    {
        return $this->ships;
    }
    
    /**
     * @param Ship[] $ships
     *
     * @return Grid
     */
    public function setShips(?array $ships = []): Grid
    {
        $this->ships = new ArrayCollection($ships);
        
        return $this;
    }
    
    /**
     * @return Shot[]|ArrayCollection
     */
    public function getShots()
    {
        return $this->shots;
    }
    
    /**
     * @param Shot[] $shots
     *
     * @return Grid
     */
    public function setShots(?array $shots = []): Grid
    {
        $this->shots = new ArrayCollection($shots);
        
        return $this;
    }
    
    public function getShipCoordinates()
    {
        $coords = [];
        
        foreach ($this->ships as $ship) {
            $coords = array_merge($coords, $ship->getCoordinates());
        }
        
        return $coords;
    }
    
    /**
     * @param Ship $ship
     *
     * @return bool
     *
     * @Serializer\Ignore()
     */
    public function isShipOverlapping(Ship $ship): bool
    {
        return (bool)array_intersect($this->getShipCoordinates(), $ship->getCoordinates());
    }
}
