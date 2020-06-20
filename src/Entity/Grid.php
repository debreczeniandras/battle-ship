<?php

namespace App\Entity;

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
     */
    private $shots;
    
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
    
    /**
     * @return Ship[]
     */
    public function getShips(): array
    {
        return $this->ships;
    }
    
    /**
     * @param Ship[] $ships
     *
     * @return Grid
     */
    public function setShips(array $ships): Grid
    {
        $this->ships = $ships;
        
        return $this;
    }
    
    /**
     * @return Shot[]
     */
    public function getShots(): array
    {
        return $this->shots;
    }
    
    /**
     * @param Shot[] $shots
     *
     * @return Grid
     */
    public function setShots(array $shots): Grid
    {
        $this->shots = $shots;
        
        return $this;
    }
}
