<?php

namespace App\Entity;

class Battle
{
    /** @var Player[] */
    private $players;
    
    private $state = 'place';
    
    /**
     * @return Player[]
     */
    public function getPlayers()
    {
        return $this->players;
    }
    
    /**
     * @param Player[] $players
     *
     * @return Battle
     */
    public function setPlayers($players)
    {
        $this->players = $players;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }
    
    /**
     * @param string $state
     *
     * @return Battle
     */
    public function setState($state)
    {
        $this->state = $state;
        
        return $this;
    }
}
