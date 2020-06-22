<?php

namespace App\Config;

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
}
