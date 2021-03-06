<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation as Serializer;

class Shot extends Coordinate
{
    /**
     * @Serializer\Groups({"Status", "Default"})
     */
    private bool $hit = false;
    
    /**
     * @Serializer\Groups({"Status", "Default"})
     */
    private bool $sunk = false;
    
    private bool $won = false;
    
    public function isHit(): bool
    {
        return $this->hit;
    }
    
    public function setHit($hit): Shot
    {
        $this->hit = $hit;
        
        return $this;
    }
    
    public function isSunk(): bool
    {
        return $this->sunk;
    }
    
    public function setSunk($sunk): Shot
    {
        $this->sunk = $sunk;
        
        return $this;
    }
    
    public function hasWon(): bool
    {
        return $this->won;
    }
    
    public function setWon($won): Shot
    {
        $this->won = $won;
        
        return $this;
    }
}
