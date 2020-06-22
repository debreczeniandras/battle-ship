<?php

namespace App\Helper;

use App\Config\Ship;
use App\Entity\Coordinate;

final class ShipHelper
{
    private function __construct()
    {
        // helper class, should not be instantiated
    }
    
    /**
     * Calculate End Coordinate based on the start, layout and length of a ship
     *
     * @param Coordinate $start
     * @param string     $layout
     * @param string     $shipId
     *
     * @return Coordinate
     */
    public static function calcEnd(Coordinate $start, $layout, $shipId)
    {
        $endCoordinate = new Coordinate();
        $length        = Ship::SHIPS[$shipId];
        
        switch ($layout) {
            case GridHelper::VERTICAL:
                $endCoordinate->setX($start->getX());
                $endCoordinate->setYAscii($start->getYAscii() + ($length -1));
                
                break;
            default:
            case GridHelper::HORIZONTAL:
                $endCoordinate->setX($start->getX() + ($length - 1));
                $endCoordinate->setY($start->getY());
        }
        
        return $endCoordinate;
    }
}
