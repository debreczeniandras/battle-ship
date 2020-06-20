<?php

namespace App\Config;

use App\Entity\Coordinate;
use App\Helper\GridHelper;

final class Ship
{
    const SHIPS = ["carrier" => 5,
                   "battleship" => 4,
                   "cruiser" => 3,
                   "submarine" => 3,
                   "destroyer" => 2];
    
    public static function getLength($id)
    {
        return self::SHIPS[$id] ?? null;
    }
    
    /**
     * Calculate End Coordinate based on the start and the length of the ship
     *
     * @param Coordinate $start
     * @param            $layout
     *
     * @return Coordinate
     */
    public static function getEnd(Coordinate $start, $layout, $shipId)
    {
        $endCoordinate = new Coordinate();
        $length        = self::SHIPS[$shipId];
        
        switch ($layout) {
            case GridHelper::VERTICAL:
                $endCoordinate->setX($start->getX());
                // reconvert letter to int, then add the desired length of the ship
                $endCoordinate->setY(chr($start->getYAscii() + 64 + ($length - 1)));
                
                break;
            default:
            case GridHelper::HORIZONTAL:
                $endCoordinate->setX($start->getX() + ($length - 1));
                $endCoordinate->setY($start->getY());
        }
        
        return $endCoordinate;
    }
}
