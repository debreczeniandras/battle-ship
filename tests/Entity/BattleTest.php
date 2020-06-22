<?php

namespace App\Tests\Entity;

use App\Entity\Battle;
use App\Entity\Coordinate;
use App\Entity\GameOptions;
use App\Entity\Player;
use App\Entity\Ship;
use PHPUnit\Framework\TestCase;

class BattleTest extends TestCase
{
    /**
     * @dataProvider provideOptionsAndShips
     *
     * @param GameOptions $options
     * @param Ship        $ship
     * @param             $expResult
     */
    public function testReturnsCorrectResultIfShipsAreOffBoard(GameOptions $options, Ship $ship, $expResult)
    {
        $battle = new Battle();
        $battle->setOptions($options);
        
        $this->assertSame($expResult, $battle->isShipOffTheBoard($ship));
    }
    
    public function provideOptionsAndShips()
    {
        $option1 = (new GameOptions())->setWidth(8)->setHeight(8);
        $option2 = (new GameOptions())->setWidth(10)->setHeight(10);
        
        return [
            [$option1,
             (new Ship())->setStart((new Coordinate())->setX(9)->setY('D'))
                         ->setEnd((new Coordinate())->setX(9)->setY('F')),
             true],
            [$option1,
             (new Ship())->setStart((new Coordinate())->setX(8)->setY('D'))
                         ->setEnd((new Coordinate())->setX(8)->setY('I')),
             true],
            [$option1,
             (new Ship())->setStart((new Coordinate())->setX(8)->setY('D'))
                         ->setEnd((new Coordinate())->setX(8)->setY('F')),
             false],
            [$option2,
             (new Ship())->setStart((new Coordinate())->setX(9)->setY('D'))
                         ->setEnd((new Coordinate())->setX(9)->setY('F')),
             false],
            [$option2,
             (new Ship())->setStart((new Coordinate())->setX(11)->setY('D'))
                         ->setEnd((new Coordinate())->setX(11)->setY('I')),
             true],
            [$option1,
             (new Ship())->setStart((new Coordinate())->setX(8)->setY('D'))
                         ->setEnd((new Coordinate())->setX(8)->setY('F')),
             false],
        ];
    }
    
    public function testIfCorrectOpponentReturned()
    {
        $battle = new Battle();
        $player1 = (new Player())->setId('A');
        $player2 = (new Player())->setId('B');
        
        $battle->addPlayer($player1)->addPlayer($player2);
        
        $this->assertSame($player2, $battle->getOpponent($player1));
    }
    
    public function testIfOnlyUniquePlayersAdded()
    {
        $battle = new Battle();
        $player1 = (new Player())->setId('A');
        $player2 = (new Player())->setId('A');
        
        $battle->addPlayer($player1)->addPlayer($player2);
        
        $this->assertCount(1, $battle->getPlayers());
    }
}
