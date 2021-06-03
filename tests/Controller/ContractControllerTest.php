<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContractControllerTest extends WebTestCase
{
    public function testWelcomePageIsUp()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
	   echo $client->getResponse()->getContent();
       echo "hey";
    }
}