<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Battle
{
    /**
     * @var string
     *
     * @Serializer\Groups({"Default", "Init"})
     */
    private $id;
    
    /**
     * The game options
     *
     * @var GameOptions
     *
     * @Assert\NotBlank()
     * @Assert\Valid()
     * @Serializer\Groups({"Default", "Init"})
     */
    private $options;
    
    /**
     * The Players of the battle
     *
     * @var Player[]|ArrayCollection
     *
     * @Assert\Count(min="2", max="2")
     * @Assert\Valid()
     * @Serializer\Groups({"Default"})
     */
    private $players;
    
    /**
     * The state of the battle
     *
     * @var string
     *
     * @Serializer\Groups({"Default", "Init"})
     */
    private $state = 'waiting';
    
    public function __construct()
    {
        $this->players = new ArrayCollection();
    }
    
    /**
     * @return Player[]|ArrayCollection
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
    public function setPlayers(array $players): Battle
    {
        $this->players = new ArrayCollection($players);
        
        return $this;
    }
    
    /**
     * @param Player $player
     *
     * @return Battle
     */
    public function addPlayer(Player $player): Battle
    {
        if (!$this->players->exists(function ($key, Player $item) use ($player) {
            return $item->getId() == $player->getId();
        })) {
            $this->players->add($player);
        }
        
        return $this;
    }
    
    /**
     * @param Player $player
     *
     * @return bool
     */
    public function hasPlayer(Player $player): bool
    {
        return $this->players->exists(function ($key, Player $item) use ($player) {
            return $item->getId() == $player->getId();
        });
    }
    
    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }
    
    /**
     * @param string $state
     *
     * @return Battle
     */
    public function setState(string $state): Battle
    {
        $this->state = $state;
        
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
    
    public static function createNewFromOptions(GameOptions $options): Battle
    {
        $battle = new static();
        $battle->setId(hash("crc32b", hash('sha256', uniqid(mt_rand(), true), true)));
        $battle->setOptions($options);
        
        return $battle;
    }
    
    /**
     * @param ExecutionContextInterface $context
     * @param                           $payload
     *
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        $playerIds = [];
        
        $width  = $this->getOptions()->getWidth();
        $height = $this->getOptions()->getHeight();
        
        $i = 0;
        foreach ($this->players as $player) {
            // check for duplicate users
            if (in_array($player->getId(), $playerIds)) {
                $context->buildViolation(sprintf('person "%s" already exists.', $player->getId()))
                        ->atPath('players')
                        ->addViolation();
                
                // if this user already exists, then skip the rest of the validation altogether.
                return;
            }
            
            // check the health of the grid
            if ($grid = $player->getGrid()) {
                $j = 0;
                
                $shipIds = [];
                // check ships
                foreach ($grid->getShips() ?? [] as $ship) {
                    
                    // if the provided coordinates are within the range of the board
                    // the minimum value of X "1" is already checked in the Entity (Greaterorequal constraint)
                    switch (true) {
                        case $ship->getStart()->getX() > $width:
                        case $ship->getEnd()->getX() > $width:
                        case $ship->getStart()->getYAscii() > $height:
                        case $ship->getEnd()->getYAscii() > $height:
                            $context->buildViolation(sprintf('This ship is off the board.'))
                                    ->atPath("players[$i].grid.ships[$j].id")
                                    ->addViolation();
                    }
                    
                    // check if ships are unique
                    if (in_array($ship->getId(), $shipIds)) {
                        $context->buildViolation(sprintf('This ship is already added to the board.'))
                                ->atPath("players[$i].grid.ships[$j].id")
                                ->addViolation();
                    }
                    
                    $shipIds[] = $ship->getId();
                    
                    // check if ships have the correct sizes
                    if ($ship->getLength() !== \App\Config\Ship::getLength($ship->getId())) {
                        if (in_array($ship->getId(), $shipIds)) {
                            $context->buildViolation(sprintf('A %s ship is supposed to be %d long. [%s]',
                                                             $ship->getId(),
                                                             \App\Config\Ship::getLength($ship->getId()),
                                                             implode(',', $ship->getCoordinates())))
                                    ->atPath("players[$i].grid.ships[$j].id")
                                    ->addViolation();
                        }
                    }
                    
                    $j++;
                }
            }
            
            $i++;
            $playerIds[] = $player->getId();
        }
    }
}
