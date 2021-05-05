<?php

namespace tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
// use Symfony\Component\HTTPFoundation\Response;

class ContractControllerTest extends WebTestCase
{
    public function testHomepageIsUp()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
}
