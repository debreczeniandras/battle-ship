<?php

namespace App\Helper;

use App\Entity\Battle;
use App\Entity\Player;
use App\Entity\Shot;
use App\Helper\ValueObject\HitSeries;

final class ShotHelper
{
    private function __construct()
    {
        // helper class, should not be instantiated
    }
    
    /**
     * This method decides if we can have a designated shot  or if we need to fire a random shot.
     *
     * @param Battle $battle
     * @param Player $player
     *
     * @return Shot
     */
    public static function getBestShot(Battle $battle, Player $player): Shot
    {
        $lastHitSeries = static::getHitSeries($battle, $player);
        if (!$lastHitSeries->count()) {
            return static::getRandomShot($battle, $player);
        }
        
        return static::getDesignatedShot($lastHitSeries, $player);
    }
    
    /**
     * Collect a list of last hits into a series to get a better shot around it.
     *
     * @param Battle $battle
     * @param Player $player
     *
     * @return HitSeries
     */
    private static function getHitSeries(Battle $battle, Player $player): HitSeries
    {
        $playerShots   = $player->getGrid()->getShots()->toArray();
        $lastHitSeries = new HitSeries($battle->getOptions()->getWidth(), $battle->getOptions()->getHeight());
        
        $item = end($playerShots);
        do {
            // no shots were yet fired
            if (!$item) {
                break;
            }
            
            // collect the last hits in a series
            if ($item->isHit()) {
                $lastHitSeries->addHit($item);
            }
            
            $item = prev($playerShots);
            // cycle should run until we reach a hit that was sunk
        } while ($item && !$item->isSunk());
        
        return $lastHitSeries;
    }
    
    /**
     * We check around the hit series, and we try to fire around it in this order. ['top', 'bottom', 'left', 'right']
     * This order stands for single hits which can not yet be horizontal or vertical.
     *
     * @param HitSeries $hitSeries
     * @param Player    $player
     *
     * @return Shot
     */
    private static function getDesignatedShot(HitSeries $hitSeries, Player $player): Shot
    {
        $nextTop    = $hitSeries->getNextTop();
        $nextBottom = $hitSeries->getNextBottom();
        $nextLeft   = $hitSeries->getNextLeft();
        $nextRight  = $hitSeries->getNextRight();
        
        switch (true) {
            case $hitSeries->isHorizontal():
                $bestShots = [$nextLeft, $nextRight];
                break;
            case $hitSeries->isVertical():
                $bestShots = [$nextTop, $nextBottom];
                break;
            default:
                $bestShots = [$nextTop, $nextBottom, $nextLeft, $nextRight];
        }
        
        foreach ($bestShots as $bestShot) {
            if (!$player->getGrid()->hasShot($bestShot)) {
                return $bestShot;
            }
        }
    }
    
    private static function getRandomShot(Battle $battle, Player $player)
    {
        $width  = $battle->getOptions()->getWidth();
        $height = $battle->getOptions()->getHeight();
        
        do {
            $x = rand(1, $width);
            $y = rand(1, $height);
            
            $shot = (new Shot())->setX($x)->setYAscii($y);
        } while ($player->getGrid()->hasShot($shot));
        
        return $shot;
    }
}
