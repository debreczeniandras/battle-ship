<?php

namespace App\Entity;

use Swagger\Annotations as SWG;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class Ship
{
    const CHOICES = ["carrier" => "carrier",
                     "battleship" => "battleship",
                     "cruiser" => "cruiser",
                     "submarine" => "submarine",
                     "destroyer" => "destroyer"];
    
    /**
     * The id of the ship. Use one of the choices
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Choice(choices=Ship::CHOICES)
     * @Serializer\Groups({"Default"})
     */
    private $id;
    
    /**
     * The start location of the ship.
     *
     * @var Coordinate
     *
     * @Assert\NotBlank()
     * @Assert\Valid()
     * @Serializer\Groups({"Default"})
     */
    private $start;
    
    /**
     * The end location of the ship.
     *
     * @var Coordinate
     *
     * @Assert\NotBlank()
     * @Assert\Valid()
     * @Serializer\Groups({"Default"})
     */
    private $end;
    
    /**
     * @return string
     */
    public function getId(): ?string
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
    public function getStart(): ?Coordinate
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
    public function getEnd(): ?Coordinate
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
    
    /**
     * @return string[]
     *
     * @SWG\Items(type="string")
     */
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
    
    /**
     * The length can be determined based on the subtraction of both start and end coordinates
     *
     * @return int
     */
    public function getLength(): int
    {
        return ($this->end->getX() - $this->start->getX()) + ($this->end->getYAscii() - $this->start->getYAscii() + 1);
    }
}
