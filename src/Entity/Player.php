<?php

namespace App\Entity;

use App\Enum\PlayerType;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class Player
{
    const PLAYERTYPES = ["regular" => PlayerType::REGULAR, "computer" => PlayerType::COMPUTER];
    
    /**
     * The id of the player (A or B)
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Choice(choices={"A", "B"}, message="That is not a valid player id.")
     * @Serializer\Groups({"Default", "Set", "Status"})
     */
    private $id;
    
    /**
     * The player type. (0 => regular, 1 => computer)
     *
     * @var int
     *
     * @Assert\Choice(choices=Player::PLAYERTYPES, message="That is not a valid player type.")
     * @Assert\NotBlank()
     * @Serializer\Groups({"Default", "Set", "Status"})
     */
    private $type = PlayerType::REGULAR;
    
    /**
     * @var Grid
     *
     * @Assert\NotBlank()
     * @Assert\Valid()
     * @Serializer\Groups({"Default", "Set", "Status"})
     */
    private $grid;
    
    /**
     * The state of the shooter. Player is either ducking or shooting.
     *
     * @var string
     *
     * @Serializer\Groups({"Default", "Status"})
     */
    private $state = 'shooting';
    
    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }
    
    /**
     * @param string $id
     *
     * @return Player
     */
    public function setId(string $id): Player
    {
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }
    
    /**
     * @param int $type
     *
     * @return Player
     */
    public function setType(int $type): Player
    {
        $this->type = $type;
        
        return $this;
    }
    
    /**
     * @return Grid
     */
    public function getGrid(): ?Grid
    {
        return $this->grid;
    }
    
    /**
     * @param Grid $grid
     *
     * @return Player
     */
    public function setGrid(Grid $grid): Player
    {
        $this->grid = $grid;
        
        return $this;
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
     * @return Player
     */
    public function setState(string $state): Player
    {
        $this->state = $state;
        
        return $this;
    }
}
