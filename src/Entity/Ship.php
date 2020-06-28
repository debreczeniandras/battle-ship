<?php

namespace App\Entity;

use Swagger\Annotations as SWG;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Ship
{
    const CHOICES = ["carrier" => "carrier",
                     "battleship" => "battleship",
                     "cruiser" => "cruiser",
                     "submarine" => "submarine",
                     "destroyer" => "destroyer"];
    
    /**
     * The id of the ship. Use one of the choices
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Choice(choices=Ship::CHOICES)
     * @Serializer\Groups({"Default", "Set"})
     */
    private $id;
    
    /**
     * The start location of the ship.
     *
     * @var Coordinate
     *
     * @Assert\NotBlank()
     * @Assert\Valid()
     * @Serializer\Groups({"Default", "Set"})
     */
    private $start;
    
    /**
     * The end location of the ship.
     *
     * @var Coordinate
     *
     * @Assert\NotBlank()
     * @Assert\Valid()
     * @Serializer\Groups({"Default", "Set"})
     */
    private $end;
    
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
     * @return Ship
     */
    public function setId(string $id): Ship
    {
        $this->id = $id;
        
        return $this;
    }
    
    public function getStart(): ?Coordinate
    {
        return $this->start;
    }
    
    public function setStart(Coordinate $start): Ship
    {
        $this->start = $start;
        
        return $this;
    }
    
    public function getEnd(): ?Coordinate
    {
        return $this->end;
    }
    
    public function setEnd(Coordinate $end): Ship
    {
        $this->end = $end;
        
        return $this;
    }
    
    /**
     * A ship is diagonal, if either `start.x` and `end.x` OR `start.y` and `end.y` are the same
     */
    public function isShipDiagonal(): bool
    {
        return !($this->start->getX() === $this->end->getX() || $this->start->getY() === $this->end->getY());
    }
    
    /**
     * @return string[]
     *
     * @SWG\Items(type="string")
     */
    public function getCoordinates(): array
    {
        $coords = [];
        
        for ($i = $this->start->getX(); $i <= $this->end->getX(); $i++) {
            // convert string to ASCII value int
            for ($j = $this->start->getYAscii(); $j <= $this->end->getYAscii(); $j++) {
                $coords[] = (string)$i . chr(64 + $j);
            }
        }
        
        return $coords;
    }
    
    /**
     * The length can be determined based on the subtraction of both start and end coordinates
     */
    public function getLength(): int
    {
        return ($this->end->getX() - $this->start->getX()) + ($this->end->getYAscii() - $this->start->getYAscii() + 1);
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
        if ($this->isShipDiagonal()) {
            $context->buildViolation('The ship is supposed to be either vertical or horizontal.')
                    ->addViolation();
        }
    }
}
