<?php

namespace App\Helper;

use App\Entity\Battle;
use App\Entity\Player;
use App\Entity\Shot;

final class ShotHelper
{
    public static function getBestShot(Battle $battle, Player $player)
    {
        // no shots have yet been fired, get random
        if (!$player->getGrid()->getShots()->count()) {
            return static::getRandomShot($battle, $player);
        }
        
        $allHits = $player->getGrid()->getShots()->filter(function (Shot $shot) {
            return $shot->isHit();
        });
        
        // no hits yet at all - get random
        if (!$allHits->count()) {
            return static::getRandomShot($battle, $player);
        }
        
        /** @var Shot $lastShot */
        $lastShot = $player->getGrid()->getShots()->last();
        // last shot was sunk, we are finished with this ship
        if ($lastShot->isSunk()) {
            return static::getRandomShot($battle, $player);
        }
        
        // if last was hit but it was not sunk, we should get a closer hit
        if ($lastShot->isHit()) {
            return static::getCloserShot($battle, $player, $lastShot);
        }
        
        // check last hit
        /** @var Shot $lastHit */
        $lastHit = $allHits->last();
        // if this hit was not sunk, we better get a closer shot
        if (!$lastHit->isSunk()) {
            return static::getCloserShot($battle, $player, $lastHit);
        }
        
        return static::getRandomShot($battle, $player);
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
    
    private static function getCloserShot(Battle $battle, Player $player, Shot $lastShot)
    {
        $width  = $battle->getOptions()->getWidth();
        $height = $battle->getOptions()->getHeight();
        
        // get all coordinates around the current shot
        $closerShot = null;
        
        // if we are not in the first row
        if ($lastShot->getX() !== 1) {
            $top = (clone $lastShot)->setX($lastShot->getX() - 1);
            if (!$player->getGrid()->hasShot($top)) {
                return $top;
            }
        }
        
        // if we are not in the last row
        if ($lastShot->getX() < $height) {
            $bottom = (clone $lastShot)->setX($lastShot->getX() + 1);
            if (!$player->getGrid()->hasShot($bottom)) {
                return $bottom;
            }
        }
        
        // if we are not in the left most column
        if ($lastShot->getYAscii() !== 1) {
            $left = (clone $lastShot)->setYAscii($lastShot->getYAscii() - 1);
            if (!$player->getGrid()->hasShot($left)) {
                return $left;
            }
        }
        
        // if we are not in the right most column
        if ($lastShot->getYAscii() < $width) {
            $right = (clone $lastShot)->setYAscii($lastShot->getYAscii() + 1);
            if (!$player->getGrid()->hasShot($right)) {
                return $right;
            }
        }
    }
}
