<?php

namespace App\Entity;

class Shot extends Coordinate
{
    private bool $hit  = false;
    private bool $sunk = false;
    
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
}
