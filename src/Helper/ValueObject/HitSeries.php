<?php

namespace App\Helper\ValueObject;

use App\Entity\Shot;

class HitSeries implements \Countable
{
    private int $width;
    private int $height;
    
    /** @var Shot[] $hits */
    private array $hits = [];
    
    public function __construct($width, $height)
    {
        $this->width  = $width;
        $this->height = $height;
    }
    
    public function addHit(Shot $shot): HitSeries
    {
        $this->hits[] = $shot;
        
        return $this;
    }
    
    public function getHits(): array
    {
        return $this->hits;
    }
    
    public function getNextShot()
    {
    
    }
    
    public function count()
    {
        return count($this->hits);
    }
}