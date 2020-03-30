<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccountControllerTest extends WebTestCase
{

    public function testLogin()
    {
        $url = '/login';
        $client = static::createClient();

        $client->request('GET', $url);
        self::assertEquals(200, $client->getResponse()->getStatusCode());

    }

    public function testLogout()
    {
        $url = '/logout';
        $client = static::createClient();

        $client->request('GET', $url);
        self::assertEquals(302, $client->getResponse()->getStatusCode());

    }
}