<?php

namespace App\Helper;

use App\Entity\Battle;
use App\Entity\Player;
use App\Entity\Shot;

final class ShotHelper
{
    public static function getBestShot(Battle $battle, Player $player)
    {
        return static::getRandomShot($battle, $player);
    }
    
    public static function getRandomShot(Battle $battle, Player $player)
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
}
