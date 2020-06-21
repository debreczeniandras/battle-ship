<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation as Serializer;

class Shot extends Coordinate
{
    /**
     * @var bool
     *
     * @Serializer\Groups("Status", "Default")
     */
    private bool $hit  = false;
    
    private bool $sunk = false;
    private bool $won  = false;
    
    public function isHit(): bool
    {
        return $this->hit;
    }
    
    public function setHit(): Shot
    {
        $this->hit = true;
        
        return $this;
    }
    
    public function isSunk(): bool
    {
        return $this->sunk;
    }
    
    public function setSunk(): Shot
    {
        $this->sunk = true;
        
        return $this;
    }
    
    public function hasWon(): bool
    {
        return $this->won;
    }
    
    public function setWon(): Shot
    {
        $this->won = true;
        
        return $this;
    }
}
