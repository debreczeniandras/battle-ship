<?php

namespace App\Entity;

class Battle
{
    /** @var Player[] */
    private $players;
    
    /** @var string */
    private $currentState = 'start';
    
    /**
     * @return Player[]
     */
    public function getPlayers(): array
    {
        return $this->players;
    }
    
    /**
     * @param Player[] $players
     *
     * @return Battle
     */
    public function setPlayers(array $players): Battle
    {
        $this->players = $players;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCurrentState(): string
    {
        return $this->currentState;
    }
    
    /**
     * @param string $currentState
     *
     * @return Battle
     */
    public function setCurrentState(string $currentState): Battle
    {
        $this->currentState = $currentState;
        
        return $this;
    }
}
