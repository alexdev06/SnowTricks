<?php

namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TrickControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        self::assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testShow()
    {
        $slug = 'mute';
        $url = '/trick/' . $slug;
        $client = static::createClient();
        $client->request('GET', $url);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testDelete()
    {
        $slug = 'mute';
        $url = '/trick/' . $slug;
        $client = static::createClient();
        $client->request('GET', $url);
        self::assertEquals(200, $client->getResponse()->getStatusCode());        
    }
    
    public function testRemoveImage()
    {
        $id = 234;
        $url = '/image' . $id;
        $client = static::createClient();
        $client->request('GET', $url);
        self::assertEquals(302, $client->getResponse()->getStatusCode()); 
    }
}
