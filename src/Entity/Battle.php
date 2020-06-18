<?php

namespace App\Entity;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class Battle
{
    /**
     * The Players of the battle
     *
     * @var Player[]
     *
     * @Assert\Count(min="2", max="2")
     * @Groups({"place"})
     */
    private $players;
    
    /**
     * The state of the battle
     *
     * @var string
     */
    private $currentState = 'ready';
    
    public function __construct()
    {
        $this->players = new ParameterBag();
    }
    
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
