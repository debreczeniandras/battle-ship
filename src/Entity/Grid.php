<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Grid
{
    const WIDTH = 8;
    const HEIGHT = 8;
    
    /**
     * The placement of the Ships.
     *
     * @var Ship[]|ArrayCollection
     *
     * @Serializer\Groups({"Default"})
     * @Assert\Count(min="5", max="5")
     * @Assert\Valid()
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
    
    /**
     * check against all the coordingate of the grid
     * or check ONE ship against all the other coordinates on the board
     *
     * @param Ship|null $exclude
     *
     * @return array
     */
    public function getShipCoordinates(Ship $exclude = null)
    {
        $coords = [];
        
        foreach ($this->ships as $ship) {
            if ($exclude && $ship->getId() === $exclude->getId()) {
                continue;
            }
            
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
        return (bool)array_intersect($this->getShipCoordinates($ship), $ship->getCoordinates());
    }
    
    /**
     * @param ExecutionContextInterface $context
     * @param                           $payload
     *
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        $i = 0;
        foreach ($this->ships as $ship) {
            if ($this->isShipOverlapping($ship)) {
                $context->buildViolation(sprintf('The "%s" ship is overlapping on the board.', $ship->getId()))
                        ->atPath("ships[$i].id")
                        ->addViolation();
            }
            
            $i++;
        }
    }
}
