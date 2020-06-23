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
     * @dataProvider provideHitSeriesForOrientation
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

    public function provideHitSeriesForOrientation()
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

    /**
     * @dataProvider provideHitSeriesForBelong
     * @testdox      If only hits are added to the series, that really belong
     *
     * @param Shot[] $hits
     * @param int    $expCount
     */
    public function testBelongsToSeries(array $hits, int $expCount)
    {
        $hitSeries = (new HitSeries(8, 8))->setHits($hits);
        
        $this->assertCount($expCount, $hitSeries);
    }
    
    public function provideHitSeriesForBelong()
    {
        return [
            'correct linear' => [
                [
                    (new Shot())->setX(2)->setY('A'),
                    (new Shot())->setX(2)->setY('B'),
                ],
                2,
            ],
            'with a duplicate' => [
                [
                    (new Shot())->setX(2)->setY('A'),
                    (new Shot())->setX(2)->setY('B'),
                    (new Shot())->setX(2)->setY('B'),
                ],
                2,
            ],
            'with several duplicates' => [
                [
                    (new Shot())->setX(2)->setY('A'),
                    (new Shot())->setX(2)->setY('A'),
                    (new Shot())->setX(2)->setY('B'),
                    (new Shot())->setX(2)->setY('B'),
                    (new Shot())->setX(2)->setY('C'),
                    (new Shot())->setX(2)->setY('C'),
                ],
                3,
            ],
            'empty set' => [
                [
                ],
                0,
            ],
        ];
    }
    
    /**
     * @dataProvider provideHitSeriesForSort
     * @testdox      If hits that are added to the series get correctly sorted
     *
     * @param Shot[] $hits
     * @param array  $expHits
     */
    public function testHitsAreSorted(array $hits, array $expHits)
    {
        $hitSeries = (new HitSeries(8, 8))->setHits($hits);
        
        $this->assertEquals($expHits, $hitSeries->getHits());
    }
    
    public function provideHitSeriesForSort()
    {
        return [
            'correct' => [
                [
                    (new Shot())->setX(2)->setY('A'),
                    (new Shot())->setX(2)->setY('B'),
                ],
                [
                    (new Shot())->setX(2)->setY('A'),
                    (new Shot())->setX(2)->setY('B'),
                ],
            ],
            'reverse' => [
                [
                    (new Shot())->setX(2)->setY('B'),
                    (new Shot())->setX(2)->setY('A'),
                ],
                [
                    (new Shot())->setX(2)->setY('A'),
                    (new Shot())->setX(2)->setY('B'),
                ],
            ],
            'random' => [
                [
                    (new Shot())->setX(2)->setY('E'),
                    (new Shot())->setX(2)->setY('A'),
                    (new Shot())->setX(2)->setY('C'),
                    (new Shot())->setX(2)->setY('D'),
                    (new Shot())->setX(2)->setY('B'),
                ],
                [
                    (new Shot())->setX(2)->setY('A'),
                    (new Shot())->setX(2)->setY('B'),
                    (new Shot())->setX(2)->setY('C'),
                    (new Shot())->setX(2)->setY('D'),
                    (new Shot())->setX(2)->setY('E'),
                ],
            ],
        ];
    }
}