<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class Coordinate
{
    /**
     * X Axis - a number
     *
     * @var int
     *
     * @Assert\Type("integer")
     * @Assert\GreaterThanOrEqual(1)
     * @Assert\NotBlank()
     * @Serializer\Groups({"Default", "Set"})
     */
    protected $x;
    
    /**
     * Y Axis - a letter
     *
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotBlank()
     * @Serializer\Groups({"Default", "Set"})
     */
    protected $y;
    
    /**
     * @return int
     */
    public function getX(): ?int
    {
        return $this->x;
    }
    
    /**
     * @param int $x
     *
     * @return Coordinate
     */
    public function setX(int $x): Coordinate
    {
        $this->x = $x;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getY(): ?string
    {
        return $this->y;
    }
    
    /**
     * Get the ASCII Int equivalent.
     *
     * @return int
     *
     * @Serializer\Ignore()
     */
    public function getYAscii(): int
    {
        return ord($this->y) - 64;
    }
    
    /**
     * @param string $y
     *
     * @return Coordinate
     */
    public function setY(string $y): Coordinate
    {
        $this->y = strtoupper($y);
        
        return $this;
    }
    
    public function __toString()
    {
        return $this->x . $this->y;
    }
}
