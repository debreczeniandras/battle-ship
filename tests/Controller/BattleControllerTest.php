<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @testdox Battle Controller
 */
class BattleControllerTest extends WebTestCase
{
    /**
     * @testdox      If set options method returns a correct battle object
     * @return string|null
     */
    public function testSetOptions(): string
    {
        $client = static::createClient();
        $client->request('POST', '/api/v1/battles', [], [], ['CONTENT_TYPE' => 'application/json'],
                         '{"width": 10, "height": 10}');
        
        $response = $client->getResponse();
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'));
        
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertNotEmpty($data['id']);
        $this->assertArrayHasKey('options', $data);
        $this->assertEquals(10, $data['options']['width']);
        $this->assertEquals(10, $data['options']['height']);
        $this->assertArrayHasKey('state', $data);
        $this->assertEquals('ready', $data['state']);
        
        return $response->headers->get('Location');
    }
    
    /**
     * @depends testSetOptions
     * @testdox Test if a Battle object is returned.
     *
     * @param $url
     *
     * @return string
     */
    public function testGetBattle($url)
    {
        $client = static::createClient();
        $client->request('GET', $url);
        
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertNotEmpty($data['id']);
        $this->assertArrayHasKey('options', $data);
        
        return $url;
    }
    
    /**
     * @dataProvider provideTestPlayerConfig
     * @depends      testGetBattle
     * @testdox      Player with grid layout set up correctly
     *
     * @param array $payload
     * @param int   $expStatus
     * @param bool  $expLocation
     * @param       $url
     */
    public function testSetUpPlayer(array $payload, int $expStatus, bool $expLocation, string $expErrorMessage, $url)
    {
        $client = static::createClient();
        $client->request('PUT', $url, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($payload));
        
        $response = $client->getResponse();
        $this->assertEquals($expStatus, $response->getStatusCode());
        $this->assertEquals($expLocation, $response->headers->has('Location'));
        $this->assertStringContainsString($expErrorMessage, $response->getContent());
    }
    
    /**
     * @depends testGetBattle
     * @testdox Battle is properly deleted.
     *
     * @param $url
     */
    public function testDeleteBattle($url)
    {
        $client = static::createClient();
        $client->request('DELETE', $url);
        
        $response = $client->getResponse();
        $this->assertEquals(204, $response->getStatusCode());
    }
    
    public function provideTestPlayerConfig()
    {
        return [
            'one ship is missing' => [
                [
                    [
                        'id' => 'A',
                        'type' => 0,
                        'grid' => [
                            'ships' => [
                                ['id' => 'carrier', 'start' => ['x' => 2, 'y' => 'A'], 'end' => ['x' => 2, 'y' => 'E']],
                                ['id' => 'cruiser', 'start' => ['x' => 4, 'y' => 'C'], 'end' => ['x' => 6, 'y' => 'C']],
                                ['id' => 'submarine', 'start' => ['x' => 4, 'y' => 'G'], 'end' => ['x' => 6, 'y' => 'G']],
                                ['id' => 'destroyer', 'start' => ['x' => 8, 'y' => 'E'], 'end' => ['x' => 8, 'y' => 'F']],
                            ],
                        
                        ],
                    ],
                    [
                        'id' => 'B',
                        'type' => 1,
                    ],
                ],
                400,
                false,
                'This collection should contain exactly'
            ],
            'correct layout' => [
                [
                    [
                        'id' => 'A',
                        'type' => 0,
                        'grid' => [
                            'ships' => [
                                ['id' => 'carrier', 'start' => ['x' => 2, 'y' => 'A'], 'end' => ['x' => 2, 'y' => 'E']],
                                ['id' => 'battleship', 'start' => ['x' => 3, 'y' => 'D'], 'end' => ['x' => 6, 'y' => 'D']],
                                ['id' => 'cruiser', 'start' => ['x' => 4, 'y' => 'C'], 'end' => ['x' => 6, 'y' => 'C']],
                                ['id' => 'submarine', 'start' => ['x' => 4, 'y' => 'G'], 'end' => ['x' => 6, 'y' => 'G']],
                                ['id' => 'destroyer', 'start' => ['x' => 8, 'y' => 'E'], 'end' => ['x' => 8, 'y' => 'F']],
                            ],
                        ],
                    ],
                    [
                        'id' => 'B',
                        'type' => 1,
                    ],
                ],
                204,
                true,
                ''
            ],
            'second attempt should no longer be allowed' => [
                [
                    [
                        'id' => 'A',
                        'type' => 0,
                        'grid' => [
                            'ships' => [
                                ['id' => 'carrier', 'start' => ['x' => 2, 'y' => 'A'], 'end' => ['x' => 2, 'y' => 'E']],
                                ['id' => 'battleship', 'start' => ['x' => 3, 'y' => 'D'], 'end' => ['x' => 6, 'y' => 'D']],
                                ['id' => 'cruiser', 'start' => ['x' => 4, 'y' => 'C'], 'end' => ['x' => 6, 'y' => 'C']],
                                ['id' => 'submarine', 'start' => ['x' => 4, 'y' => 'G'], 'end' => ['x' => 6, 'y' => 'G']],
                                ['id' => 'destroyer', 'start' => ['x' => 8, 'y' => 'E'], 'end' => ['x' => 8, 'y' => 'F']],
                            ],
                        ],
                    ],
                    [
                        'id' => 'B',
                        'type' => 1,
                    ],
                ],
                400,
                false,
                'is not allowed at this state'
            ],
        ];
    }
}
