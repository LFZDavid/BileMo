<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class ApiTest extends WebTestCase
{

    use FixturesTrait;
    private $client;

    /**
     * This method is called before each test.
     */
    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testProductList(): void
    {
        $this->client->request('GET','/api/products');
        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
    }
}
