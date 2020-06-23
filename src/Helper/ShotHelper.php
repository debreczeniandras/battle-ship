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
    
    public static function getBestShot(Battle $battle, Player $player)
    {
        $lastHitSeries = static::getHitSeries($battle, $player);
        if (!$lastHitSeries->count()) {
            return static::getRandomShot($battle, $player);
        }
        
        $nextShot = $lastHitSeries->getNextShot();
        
        return static::getRandomShot($battle, $player);
    }
    
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
            
            if ($item->isHit()) {
                $lastHitSeries->addHit($item);
            }
            
            $item = prev($playerShots);
        } while ($item && !$item->isSunk());
        
        return $lastHitSeries;
    }
    
    private static function getRandomShot(Battle $battle, Player $player)
    {
        $width  = $battle->getOptions()->getWidth();
        $height = $battle->getOptions()->getHeight();
        
        do {
            $x = rand(1, $width);
            $y = rand(1, $height);
            
            $shot = (new Shot())->setX($x)->setY(chr(64 + $y));
        } while ($player->getGrid()->hasShot($shot));
        
        return $shot;
    }
    
    private static function getCloserShot(Battle $battle, Player $player, Shot $lastShot): Shot
    {
        $surrCoord = static::getSurroundingCoordinates($battle, $lastShot);
        
        // if previous shot was below, return the one above
        if (isset($surrCoord['bottom']) && $player->getGrid()->hasShot($surrCoord['bottom'])) {
            // unless the top shot has already been fired
            // or we are not already at the top
            if (isset($surrCoord['top']) && !$player->getGrid()->hasShot($surrCoord['top'])) {
                return $surrCoord['top'];
            }
        }
        
        // if previous shot was above, return the one below
        if (isset($surrCoord['top']) && $player->getGrid()->hasShot($surrCoord['top'])) {
            // unless the bottom shot has already been fired
            // or we are not already at the bottom
            if (isset($surrCoord['bottom']) && !$player->getGrid()->hasShot($surrCoord['bottom'])) {
                return $surrCoord['bottom'];
            }
        }
        
        // if previous shot was to the left, return the one to the right
        if (isset($surrCoord['left']) && $player->getGrid()->hasShot($surrCoord['left'])) {
            // unless we are already in the right column
            // or we are not already at the bottom
            if (isset($surrCoord['right']) && !$player->getGrid()->hasShot($surrCoord['right'])) {
                return $surrCoord['right'];
            }
        }
        
        // if previous shot was to the right, return the one to the left
        if (isset($surrCoord['right']) && $player->getGrid()->hasShot($surrCoord['right'])) {
            // unless we are already in the right column
            // or we are not already at the bottom
            if (isset($surrCoord['left']) && !$player->getGrid()->hasShot($surrCoord['left'])) {
                return $surrCoord['left'];
            }
        }
        
        // if we were unable to get a tactical coordinate, return one of the surrounding ones
        shuffle($surrCoord);
        foreach ($surrCoord as $coord) {
            if (!$player->getGrid()->hasShot($coord)) {
                return $coord;
            }
        }
        
        // @todo here we may need to start shooting from the other side
        
        // if we are unable to find a shot from the surrounding coordinates that have not yet been fired return a random shot
        return static::getRandomShot($battle, $player);
    }
    
    private static function getSurroundingCoordinates(Battle $battle, Shot $lastShot)
    {
        $width  = $battle->getOptions()->getWidth();
        $height = $battle->getOptions()->getHeight();
        
        $surrCoord = [];
        
        // get next coordinate left to the last
        if ($lastShot->getX() !== 1) {
            $surrCoord['left'] = (clone $lastShot)->setX($lastShot->getX() - 1);
        }
        
        // get next coordinate right to the last
        if ($lastShot->getX() < $height) {
            $surrCoord['right'] = (clone $lastShot)->setX($lastShot->getX() + 1);
        }
        
        // get next coordinate above
        if ($lastShot->getYAscii() !== 1) {
            $surrCoord['top'] = (clone $lastShot)->setYAscii($lastShot->getYAscii() - 1);
        }
        
        // get next coordinate to the bottom
        if ($lastShot->getYAscii() < $width) {
            $surrCoord['bottom'] = (clone $lastShot)->setYAscii($lastShot->getYAscii() + 1);
        }
        
        return $surrCoord;
    }
}
