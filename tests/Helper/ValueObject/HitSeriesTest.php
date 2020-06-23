<?php

namespace App\Tests\Helper\ValueObject;

use App\Entity\Shot;
use App\Helper\ValueObject\HitSeries;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Hit Series Helper
 */
class HitSeriesTest extends TestCase
{
    /**
     * @dataProvider provideHitSeries
     * @testdox      If Orientation is correctly reported
     *
     * @param array $hits
     * @param bool  $expIsHorizontal
     * @param bool  $expIsVertical
     */
    public function testOrientation(array $hits, bool $expIsHorizontal, bool $expIsVertical)
    {
        $hitSeries = (new HitSeries(8, 8))->setHits($hits);
        
        $this->assertSame($expIsHorizontal, $hitSeries->isHorizontal());
        $this->assertSame($expIsVertical, $hitSeries->isVertical());
    }
    
    public function provideHitSeries()
    {
        return [
            'vertical' => [
                [
                    (new Shot())->setX(2)->setY('A'),
                    (new Shot())->setX(2)->setY('B'),
                ],
                false,
                true,
            ],
            'horizontal' => [
                [
                    (new Shot())->setX(5)->setY('E'),
                    (new Shot())->setX(6)->setY('E'),
                    (new Shot())->setX(7)->setY('F'),
                ],
                true,
                false,
            ],
            'one element' => [
                [
                    (new Shot())->setX(5)->setY('E'),
                ],
                false,
                false,
            ],
            'no elements' => [
                [
                ],
                false,
                false,
            ],
        ];
    }
}