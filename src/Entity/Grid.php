<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Swagger\Annotations as SWG;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Grid
{
    /**
     * The placement of the Ships.
     *
     * @var Ship[]
     *
     * @Serializer\Groups({"Default", "Set"})
     * @Assert\Count(min="5", max="5")
     * @Assert\Valid()
     */
    private $ships;
    
    /**
     * @var Shot[]
     *
     * @Serializer\Groups({"Default", "Status"})
     */
    private $shots;
    
    public function __construct()
    {
        $this->ships = new ArrayCollection();
        $this->shots = new ArrayCollection();
    }
    
    public function addShip(Ship $ship): Grid
    {
        $this->ships->add($ship);
        
        return $this;
    }
    
    public function addShot(Shot $shot): Grid
    {
        $this->shots->add($shot);
        
        return $this;
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
    
    public function hasShot(Shot $shot): bool
    {
        return $this->shots->exists(function ($key, Shot $item) use ($shot) {
            return $item->getX() === $shot->getX() && $item->getY() === $shot->getY();
        });
    }
    
    /**
     * check against all the coordingate of the grid
     * or check ONE ship against all the other coordinates on the board
     *
     * @param Ship|null $exclude
     *
     * @return string[]
     *
     * @SWG\Items(type="string")
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
     * @return string[]
     *
     * @SWG\Items(type="string")
     */
    public function getShotCoordinates()
    {
        $coords = [];
        
        foreach ($this->shots as $shot) {
            $coords[] = (string) $shot;
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
     * @param Shot $shot
     *
     * @return bool
     */
    public function isHit(Shot $shot): bool
    {
        return in_array((string)$shot, $this->getShipCoordinates());
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
