<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class Battle
{
    /**
     * @var string
     *
     * @Serializer\Groups({"init"})
     */
    private $id;
    
    /**
     * The game options
     *
     * @var GameOptions
     *
     * @Assert\NotBlank()
     * @Assert\Valid()
     * @Serializer\Groups({"init"})
     */
    private $options;
    
    /**
     * The Players of the battle
     *
     * @var Player[]
     *
     * @Assert\Count(min="2", max="2")
     */
    private $players;
    
    /**
     * The state of the battle
     *
     * @var string
     *
     * @Serializer\Groups({"init"})
     * @Serializer\SerializedName("state")
     */
    private $currentState = 'waiting';
    
    private function __construct()
    {
        // user static instance creator
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
    
    /**
     * @return GameOptions
     */
    public function getOptions(): GameOptions
    {
        return $this->options;
    }
    
    /**
     * @param GameOptions $options
     *
     * @return Battle
     */
    public function setOptions(GameOptions $options): Battle
    {
        $this->options = $options;
        
        return $this;
    }
    
    /**
     * @param $hash
     *
     * @return Battle
     */
    public function setId($hash): Battle
    {
        $this->id = $hash;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
    
    public static function newInstance()
    {
        $battle = new static();
        $battle->setId(hash("crc32b", hash('sha256', uniqid(mt_rand(), true), true)));
        
        return $battle;
    }
}
