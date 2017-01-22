<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/configuration');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCategories()
    {
        $client = static::createClient();

        $client->request('GET', '/categories');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCategory()
    {
        $client = static::createClient();

        $client->request('GET', '/category/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
