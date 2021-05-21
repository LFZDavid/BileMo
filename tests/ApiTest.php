<?php

namespace App\Tests;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class ApiTest extends WebTestCase
{

    use FixturesTrait;
    private $client;
    private ProductRepository $productRepository;

    /**
     * This method is called before each test.
     */
    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->productRepository = static::$container->get(ProductRepository::class);
    }

    public function testProductList(): void
    {
        $this->client->request('GET','/api/products');
        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
    }
    
    public function testProductShow(): void
    {
        /** Get the test product installed with fixtures */
        $product = $this->productRepository->findOneBy(['name' => 'find']);
        $this->client->request('GET','/api/products/'.$product->getId());
        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());

    }

    public function testCustomerList(): void
    {
        $this->client->request('GET','/api/customers');
        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
    }
}
