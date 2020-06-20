<?php

namespace App\Helper;

use App\Entity\Battle;
use App\Entity\Coordinate;
use App\Entity\Grid;
use App\Entity\Ship;

class GridHelper
{
    const CHOICES = ["carrier" => 5, "battleship" => 4, "cruiser" => 3, "submarine" => 3, "destroyer" => 2];
    const VERTICAL = 'vertical';
    const HORIZONTAL = 'horizontal';
    const LAYOUT = [self::HORIZONTAL, self::VERTICAL];
    
    public function getRandomGrid(Battle $battle)
    {
        $width  = $battle->getOptions()->getWidth();
        $height = $battle->getOptions()->getHeight();
        
        $grid = new Grid();
        
        foreach (self::CHOICES as $id => $length) {
            $ship = new Ship();
            $ship->setId($id);
            
            $layout = self::LAYOUT[rand(0, 1)];
            
            // define the start position of the ship
            
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
                $end   = \App\Config\Ship::getEnd($start, $layout, $ship->getId());
                
                $ship->setStart($start);
                $ship->setEnd($end);
            } while ($grid->isShipOverlapping($ship));
            
            $grid->addShip($ship);
        }
        
        return $grid;
    }
    
    public static function XChoices()
    {
        return range(1, self::WIDTH);
    }
    
    public static function YChoices()
    {
        return range(1, chr(65 + self::HEIGHT));
    }
    
}
