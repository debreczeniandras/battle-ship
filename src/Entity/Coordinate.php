<?php

namespace App\Entity;

class Coordinate
{
    /** @var int */
    private $x;
    
    /** @var string */
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
