<?php

namespace App\Tests\Helper;

use App\Entity\Battle;
use App\Entity\GameOptions;
use App\Entity\Grid;
use App\Entity\Player;
use App\Entity\Shot;
use App\Helper\ShotHelper;
use App\Helper\ValueObject\HitSeries;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Shot Helper Class
 */
class ShotHelperTest extends TestCase
{
    /**
     * @dataProvider provideShots
     *
     * @param Shot   $lastShot
     * @param Shot[] $expSurrCoords
     *
     * @throws \ReflectionException
     * @testdox      Last Hit Series are returned correctly
     */
    public function testLastHitSeries(array $shots, $width, $height, HitSeries $expSeries)
    {
        $battle = (new Battle())->setOptions((new GameOptions())->setWidth($width)->setHeight($height));
        $player = (new Player())->setGrid((new Grid())->setShots($shots));
        
        $reflection = new \ReflectionClass(ShotHelper::class);
        $method     = $reflection->getMethod('getHitSeries');
        $method->setAccessible(true);
        
        $this->assertEquals($expSeries, $method->invokeArgs(null, [$battle, $player]));
    }
    
    public function provideShots()
    {
        return [
            'with an unfinished ship' => [
                [
                    (new Shot())->setX(2)->setY('A'),
                    (new Shot())->setX(2)->setY('B'),
                    (new Shot())->setX(3)->setY('B')->setHit(true),
                    (new Shot())->setX(3)->setY('C')->setHit(true),
                    (new Shot())->setX(3)->setY('D')->setHit(true),
                    (new Shot())->setX(3)->setY('A'),
                ],
                8,
                8,
                (new HitSeries(8, 8))->addHit((new Shot())->setX(3)->setY('D')->setHit(true))
                                     ->addHit((new Shot())->setX(3)->setY('C')->setHit(true))
                                     ->addHit((new Shot())->setX(3)->setY('B')->setHit(true)),
            ],
            'with an already sunk ship' => [
                [
                    (new Shot())->setX(2)->setY('A'),
                    (new Shot())->setX(2)->setY('B'),
                    (new Shot())->setX(3)->setY('B')->setHit(true),
                    (new Shot())->setX(3)->setY('C')->setHit(true),
                    (new Shot())->setX(3)->setY('D')->setHit(true)->setSunk(true),
                    (new Shot())->setX(3)->setY('A'),
                ],
                8,
                8,
                (new HitSeries(8, 8)),
            ],
            'with shots belonging to different ships (only last ships` hits series expected)' => [
                [
                    (new Shot())->setX(2)->setY('A'),
                    (new Shot())->setX(2)->setY('B'),
                    (new Shot())->setX(3)->setY('B')->setHit(true),
                    (new Shot())->setX(3)->setY('C')->setHit(true),
                    (new Shot())->setX(3)->setY('D')->setHit(true),
                    (new Shot())->setX(3)->setY('A'),
                    (new Shot())->setX(2)->setY('D')->setHit(true),
                    (new Shot())->setX(3)->setY('D')->setHit(true),
                    (new Shot())->setX(4)->setY('D')->setHit(true),
                ],
                8,
                8,
                (new HitSeries(8, 8))->addHit((new Shot())->setX(4)->setY('D')->setHit(true))
                                     ->addHit((new Shot())->setX(3)->setY('D')->setHit(true))
                                     ->addHit((new Shot())->setX(2)->setY('D')->setHit(true)),
            ],
            'with no shots yet fired' => [
                [
                ],
                8,
                8,
                (new HitSeries(8, 8)),
            ],
            'with only missing shots' => [
                [
                    (new Shot())->setX(2)->setY('A'),
                    (new Shot())->setX(2)->setY('B'),
                    (new Shot())->setX(3)->setY('B'),
                    (new Shot())->setX(3)->setY('C'),
                    (new Shot())->setX(3)->setY('D'),
                    (new Shot())->setX(3)->setY('A'),
                ],
                8,
                8,
                (new HitSeries(8, 8)),
            ],
        ];
    }
}
