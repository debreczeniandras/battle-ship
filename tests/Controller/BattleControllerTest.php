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
        $client->request('POST', '/api/v1/battles', [], [], ['CONTENT_TYPE' => 'application/json'], '{"width": 10, "height": 10}');
        
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
     * @depends testGetBattle
     * @testdox Test if a Battle is deleted.
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
}
