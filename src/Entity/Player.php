<?php

namespace App\Entity;

use App\Enum\PlayerType;
use Symfony\Component\Serializer\Annotation\Groups;
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
     * @Assert\Choice(choices={"A", "B"}, message="Choose a valid player type.")
     * @Groups({"place"})
     */
    private $id;
    
    /**
     * The player type. (0 => regular, 1 => computer)
     *
     * @var int
     *
     * @Assert\Choice(choices=Player::PLAYERTYPES, message="Choose a valid player type.")
     * @Assert\NotBlank()
     * @Groups({"place"})
     */
    private $type = PlayerType::REGULAR;
    
    /**
     * @var Grid
     *
     * @Assert\NotBlank()
     * @Assert\Valid()
     * @Groups({"place"})
     */
    private $grid;
    
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
    public function getGrid(): Grid
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
}
