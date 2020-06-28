<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Swagger\Annotations as SWG;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Battle
{
    /**
     * @Serializer\Groups({"Default", "Init", "Status"})
     */
    private string $id;
    
    /**
     * @Assert\NotBlank()
     * @Assert\Valid()
     * @Serializer\Groups({"Default", "Init", "Status"})
     */
    private GameOptions $options;
    
    /**
     * The Players of the battle
     *
     * @var Player[]
     *
     * @Assert\Count(min="2", max="2")
     * @Assert\Valid()
     * @Serializer\Groups({"Default", "Status"})
     */
    private iterable $players;
    
    /**
     * The state of the battle. This is also the marking store for our state machine.
     *
     * @Serializer\Groups({"Default", "Init", "Status"})
     */
    private string $state = 'waiting';
    
    public function __construct()
    {
        $this->players = new ArrayCollection();
    }
    
    /**
     * @return Player[]|ArrayCollection
     */
    public function getPlayers(): iterable
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
        if (!$this->hasPlayer($player)) {
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
        return $this->players->exists(fn($key, Player $item) => $item->getId() == $player->getId());
    }
    
    public function getState(): string
    {
        return $this->state;
    }
    
    public function setState(string $state): Battle
    {
        $this->state = $state;
        
        return $this;
    }
    
    public function getOptions(): GameOptions
    {
        return $this->options;
    }
    
    public function setOptions(GameOptions $options): Battle
    {
        $this->options = $options;
        
        return $this;
    }
    
    public function setId(string $hash): Battle
    {
        $this->id = $hash;
        
        return $this;
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getPlayer($id): Player
    {
        return $this->getPlayers()->filter(function (Player $player) use ($id) {
            return $player->getId() === $id;
        })->first();
    }
    
    /**
     * @param Player $player
     *
     * @return Player
     *
     * @SWG\Parameter(type=Player::class)
     */
    public function getOpponent(Player $player): Player
    {
        return $this->getPlayers()->filter(fn (Player $item) => $item->getId() !== $player->getId())->first();
    }
    
    public static function createNewFromOptions(GameOptions $options): Battle
    {
        $battle = new static();
        $battle->setId(hash("crc32b", hash('sha256', uniqid(mt_rand(), true), true)));
        $battle->setOptions($options);
        
        return $battle;
    }
    
    /**
     * Checks if the ship coordinates are within the range of the board.
     * We have to check it here, since we only know the size of the board here.
     *
     * @param Ship $ship
     *
     * @return bool
     */
    public function isShipOffTheBoard(Ship $ship): bool
    {
        $width  = $this->getOptions()->getWidth();
        $height = $this->getOptions()->getHeight();
        
        switch (true) {
            case $ship->getStart()->getX() < 1:
            case $ship->getStart()->getYAscii() < 1:
            case $ship->getStart()->getX() > $width:
            case $ship->getEnd()->getX() > $width:
            case $ship->getStart()->getYAscii() > $height:
            case $ship->getEnd()->getYAscii() > $height:
                return true;
            
            default:
                return false;
        }
    }
    
    /**
     * Most of the validation needs to happen here, because they may depend on configurations defined in the battle.
     * We may not have access to the battle from within the grid of a user, that's why we validate it here.
     *
     * @param ExecutionContextInterface $context
     * @param                           $payload
     *
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        $playerIds = [];
        
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
                    if ($this->isShipOffTheBoard($ship)) {
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
