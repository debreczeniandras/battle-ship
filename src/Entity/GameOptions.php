<?php

namespace App\Entity;

use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

class GameOptions
{
    /**
     * The width of the board.
     *
     * @var int
     *
     * @Assert\Type("integer")
     * @Assert\Range(min="8", max="15", minMessage="This value should be greater than 8")
     * @SWG\Property(default="8")
     */
    private $width = 8;
    
    /**
     * The height of the board.
     *
     * @var int
     *
     * @Assert\Type("integer")
     * @Assert\Range(min="8", max="15")
     * @SWG\Property(default="8")
     */
    private $height = 8;
    
    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }
    
    /**
     * @param int $width
     *
     * @return GameOptions
     */
    public function setWidth(int $width): GameOptions
    {
        $this->width = $width;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }
    
    /**
     * @param int $height
     *
     * @return GameOptions
     */
    public function setHeight(int $height): GameOptions
    {
        $this->height = $height;
        
        return $this;
    }
}
