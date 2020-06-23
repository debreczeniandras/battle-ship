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
        if (!$this->exists($shot)) {
            if ($this->belongsToSeries($shot)) {
                $this->hits[] = $shot;
            }
        }
        
        usort($this->hits, function ($a, $b) {
            return strcasecmp((string) $a, (string) $b);
        });
        
        return $this;
    }
    
    public function setHits(array $hits): HitSeries
    {
        foreach ($hits as $hit) {
            $this->addHit($hit);
        }
        
        return $this;
    }
    
    /**
     * @return Shot[]
     */
    public function getHits(): array
    {
        return $this->hits;
    }
    
    /**
     * if only one element exists, then we should allow any new shots
     * otherwise check if shot fits horizontally or vertically
     *
     * @param Shot $shot
     *
     * @return bool
     */
    public function belongsToSeries(Shot $shot): bool
    {
        switch (true) {
            case $this->isHorizontal():
                return $this->hits[0]->getY() === $shot->getY();
            case $this->isVertical():
                return $this->hits[0]->getX() === $shot->getX();
            default:
                return true;
        }
    }
    
    public function exists(Shot $shot)
    {
        $exists = false;
        foreach ($this->hits as $hit) {
            if ($hit->getY() === $shot->getY() && $hit->getX() === $shot->getX()) {
                $exists = true;
                break;
            }
        }
        
        return $exists;
    }
    
    public function isHorizontal(): bool
    {
        if ($this->count() < 2) {
            return false;
        }
        
        return $this->hits[0]->getY() === $this->hits[1]->getY();
    }
    
    public function isVertical(): bool
    {
        if ($this->count() < 2) {
            return false;
        }
        
        return $this->hits[0]->getX() === $this->hits[1]->getX();
    }
    
    public function count()
    {
        return count($this->hits);
    }
}
