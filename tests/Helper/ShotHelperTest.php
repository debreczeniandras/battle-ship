<?php

namespace App\Tests\Helper;

use App\Entity\Battle;
use App\Entity\GameOptions;
use App\Entity\Shot;
use App\Helper\ShotHelper;
use PHPUnit\Framework\TestCase;

class ShotHelperTest extends TestCase
{
    /**
     * @dataProvider provideShots
     *
     * @param Shot   $lastShot
     * @param Shot[] $expSurrCoords
     *
     * @throws \ReflectionException
     */
    public function testCorrectSurroundingCoordsAreReturned(Shot $lastShot, array $expSurrCoords)
    {
        $battle = (new Battle())->setOptions((new GameOptions())->setWidth(8)->setHeight(8));
        
        $reflection = new \ReflectionClass(ShotHelper::class);
        $method     = $reflection->getMethod('getSurroundingCoordinates');
        $method->setAccessible(true);
        
        $this->assertEquals($expSurrCoords, $method->invokeArgs(null, [$battle, $lastShot]));
    }
    
    public function provideShots()
    {
        return [
            [(new Shot())->setX(5)->setY('C'),
             [
                 'left' => (new Shot())->setX(4)->setY('C'),
                 'right' => (new Shot())->setX(6)->setY('C'),
                 'top' => (new Shot())->setX(5)->setY('B'),
                 'bottom' => (new Shot())->setX(5)->setY('D'),
             ],
            ],
            [(new Shot())->setX(4)->setY('E'),
             [
                 'left' => (new Shot())->setX(3)->setY('E'),
                 'right' => (new Shot())->setX(5)->setY('E'),
                 'top' => (new Shot())->setX(4)->setY('D'),
                 'bottom' => (new Shot())->setX(4)->setY('F'),
             ],
            ],
            [(new Shot())->setX(1)->setY('A'),
             [
                 'right' => (new Shot())->setX(2)->setY('A'),
                 'bottom' => (new Shot())->setX(1)->setY('B'),
             ],
            ],
            [(new Shot())->setX(8)->setY('H'),
             [
                 'left' => (new Shot())->setX(7)->setY('H'),
                 'top' => (new Shot())->setX(8)->setY('G'),
             ],
            ],
        ];
    }
}
