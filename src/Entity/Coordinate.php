<?php

namespace App\Entity;

use App\Helper\GridHelper;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class Coordinate
{
    /**
     * X Axis - a number
     *
     * @var int
     *
     * @Groups({"place"})
     * @Assert\Type("integer")
     * @Assert\Choice(callback={"App\Entity\Grid", "XChoices"})
     */
    private $x;
    
    /**
     * Y Axis - a letter
     *
     * @var string
     *
     * @Groups({"place"})
     * @Assert\Type("string")
     * @Assert\Choice(callback={"App\Entity\Grid", "YChoices"})
     */
    private $y;
    
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
     * @param string $y
     *
     * @return Coordinate
     */
    public function setY(string $y): Coordinate
    {
        $this->y = $y;
        
        return $this;
    }
    
    /**
     * Calculate End Coordinate based on the start.
     *
     * @param Coordinate $start
     * @param            $length
     * @param            $layout
     *
     * @return static
     */
    public static function calcEnd(Coordinate $start, $length, $layout)
    {
        $endCoordinate = new static();
        
        switch ($layout) {
            case GridHelper::VERTICAL:
                $endCoordinate->setX($start->getX());
                // reconvert letter to int, then add the desired length of the ship
                $endCoordinate->setY(chr(ord($start->getY()) + ($length -1)));
                
                break;
            default:
            case GridHelper::HORIZONTAL:
                $endCoordinate->setX($start->getX() + ($length - 1));
                $endCoordinate->setY($start->getY());
        }
        
        return $endCoordinate;
    }
    
    public function __toString()
    {
        return $this->x . $this->y;
    }
}
