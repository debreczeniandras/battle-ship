<?php

namespace App\Entity;

class Battle
{
    /** @var Player[] */
    private $players;
    
    private $state = 'place';
}
