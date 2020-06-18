<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class Coordinate
{
    /**
     * X Axis - a number
     *
     * @var int
     *
     * @Groups({"place"})
     * @Assert\Type("integer")
     */
    private $x;
    
    /**
     * Y Axis - a letter
     *
     * @var string
     *
     * @Groups({"place"})
     * @Assert\Type("string")
     * @Assert\Length(min="1", max="1")
     */
    private $y;
    
    /**
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }
    
    /**
     * @param int $x
     *
     * @return Coordinate
     */
    public function setX(int $x): Coordinate
    {
        $this->x = $x;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getY(): string
    {
        return $this->y;
    }
    
    /**
     * @param string $y
     *
     * @return Coordinate
     */
    public function setY(string $y): Coordinate
    {
        $this->y = $y;
        
        return $this;
    }
}
