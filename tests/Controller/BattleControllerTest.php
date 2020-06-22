<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BattleControllerTest extends WebTestCase
{
    /**
     * @dataProvider provideOptionsForDimensions
     *
     * @param $payload
     * @param $expWidth
     * @param $expHeight
     */
    public function testSetOptionsReturnsCorrectDimensions($payload, $expWidth, $expHeight)
    {
        $client = static::createClient();
        $client->request('POST', '/api/v1/battles', [], [], ['CONTENT_TYPE' => 'application/json'], $payload);
    
        $response = $client->getResponse();
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'));
        
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertNotEmpty($data['id']);
        $this->assertArrayHasKey('options', $data);
        $this->assertEquals($expWidth, $data['options']['width']);
        $this->assertEquals($expHeight, $data['options']['height']);
        $this->assertArrayHasKey('state', $data);
        $this->assertEquals('ready', $data['state']);
    }
    
    public function provideOptionsForDimensions()
    {
        return [
            ['{}', 8, 8],
            ['{"width": 10, "height": 10}', 10, 10],
        ];
    }
}
