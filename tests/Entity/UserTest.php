<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{
    public function testGetFullName()
    {
        $User = new User();
        $User->setFirstName('Alex');
        $User->setLastName('Dubois');

        $this->assertSame('Alex Dubois', $User->getFullname());

    }
}