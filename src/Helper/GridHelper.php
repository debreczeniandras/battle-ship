<?php

namespace App\Helper;

use App\Entity\Battle;
use App\Entity\Coordinate;
use App\Entity\Grid;
use App\Entity\Ship;

final class GridHelper
{
    const CHOICES = ["carrier" => 5, "battleship" => 4, "cruiser" => 3, "submarine" => 3, "destroyer" => 2];
    const VERTICAL = 'vertical';
    const HORIZONTAL = 'horizontal';
    const LAYOUT = [self::HORIZONTAL, self::VERTICAL];
    
    private function __construct()
    {
        // helper class, should not be instantiated
    }
    
    public static function getRandomGrid(Battle $battle)
    {
        $width  = $battle->getOptions()->getWidth();
        $height = $battle->getOptions()->getHeight();
        
        $grid = new Grid();
        
        foreach (self::CHOICES as $id => $length) {
            $ship = new Ship();
            $ship->setId($id);
            
            $layout = self::LAYOUT[rand(0, 1)];
            
            do {
                // VERTICAL
                // x can be any random number up to the width of the grid
                $x = rand(1, $width);
                // y should random up to the height of the board MINUS the length of the ship
                $y = rand(1, $height - $length);
                
                // HORIZONTAL,
                if ($layout === self::HORIZONTAL) {
                    // x should be random after subtracting the length of the ship. (so it fits on the board
                    $x = rand(1, $width - $length);
                    // this can then be any in the available range
                    $y = rand(1, $height);
                }
                
                $start = (new Coordinate())->setX($x)->setY(chr(64 + $y));
                $end   = ShipHelper::calcEnd($start, $layout, $ship->getId());
                
                $ship->setStart($start);
                $ship->setEnd($end);
            } while ($grid->isShipOverlapping($ship) || $battle->isShipOffTheBoard($ship));
            
            $grid->addShip($ship);
        }
        
        return $grid;
    }
}
