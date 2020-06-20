<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class Ship
{
    const CHOICES = ["carrier", "battleship", "cruiser", "submarine", "destroyer"];
    
    /**
     * The id of the ship. Use one of the choices
     *
     * @var string
     *
     * @Assert\Choice(choices=Ship::CHOICES)
     * @Assert\NotBlank()
     * @Assert\Unique()
     * @Groups({"place"})
     */
    private $id;
    
    /**
     * The start location of the ship.
     *
     * @var Coordinate
     *
     * @Assert\NotBlank()
     * @Assert\Valid()
     * @Groups({"place"})
     */
    private $start;
    
    /**
     * The end location of the ship.
     *
     * @var Coordinate
     *
     * @Assert\NotBlank()
     * @Assert\Valid()
     * @Groups({"place"})
     */
    private $end;
    
    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
    
    /**
     * @param string $id
     *
     * @return Ship
     */
    public function setId(string $id): Ship
    {
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * @return Coordinate
     */
    public function getStart(): Coordinate
    {
        return $this->start;
    }
    
    /**
     * @param Coordinate $start
     *
     * @return Ship
     */
    public function setStart(Coordinate $start): Ship
    {
        $this->start = $start;
        
        return $this;
    }
    
    /**
     * @return Coordinate
     */
    public function getEnd(): Coordinate
    {
        return $this->end;
    }
    
    /**
     * @param Coordinate $end
     *
     * @return Ship
     */
    public function setEnd(Coordinate $end): Ship
    {
        $this->end = $end;
        
        return $this;
    }
    
    public function getCoordinates(): array
    {
        $coords = [];
        
        for ($i = $this->start->getX(); $i <= $this->end->getX(); $i++) {
            // convert string to ASCII value int
            for ($j = ord($this->start->getY()) - 64; $j <= ord($this->end->getY()) - 64; $j++) {
                $coords[] = (string)$i . chr(64 + $j);
            }
        }
        
        return $coords;
    }
}
