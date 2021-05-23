<?php

namespace App\Tests;

use App\DataFixtures\AppFixtures;
use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;
use App\Repository\SupplierRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiTest extends WebTestCase
{

    use FixturesTrait;
    private $client;
    private ProductRepository $productRepository;
    private CustomerRepository $customerRepository;
    private $supplierTest;
    private SupplierRepository $supplierRepository;

    /**
     * This method is called before each test.
     */
    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->loadFixtures([AppFixtures::class]);
        $this->productRepository = static::$container->get(ProductRepository::class);
        $this->customerRepository = static::$container->get(CustomerRepository::class);
        $this->supplierRepository = static::$container->get(SupplierRepository::class);
        $this->supplierTest = $this->supplierRepository->findOneBy(["name" => "SupplierTest"]);
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

    public function testCustomerShow(): void
    {
        $customer = $this->customerRepository->findOneBy(['name' => 'customertest0']);
        $expectedJson = '{"id":'.$customer->getId().',"name":"'.$customer->getName().'"}';
        $this->client->request('GET', '/api/customers/'. $customer->getId());
        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse()->getContent();
        $this->assertJson($response);
        $this->assertTrue($expectedJson === $response);
    }

    public function testWrongCustomerShow(): void 
    {
        $this->client->request('GET', '/api/customers/x');
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_NOT_FOUND);
        $response = $this->client->getResponse()->getContent();
        $this->assertJson($response);
    }

    public function testCreateCustomer(): void
    {
        $data = ['name' => 'new Customer'];

        $this->client->request('POST', '/api/customers', $data);
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);
        $response = $this->client->getResponse()->getContent();
        $this->assertJson($response);
    }
    
    public function testCreateCustomerWithoutData(): void
    {
        $this->client->request('POST', '/api/customers', []);
        $this->assertResponseStatusCodeSame(400);
        $response = $this->client->getResponse()->getContent();
        $this->assertJson($response);
    }
    
    public function testCreateCustomerWithBlankName(): void
    {
        $this->client->request('POST', '/api/customers', ["name"=>""]);
        $this->assertResponseStatusCodeSame(400);
        $response = $this->client->getResponse()->getContent();
        $this->assertJson($response);
    }

    public function testCreateCustomerNameAlreadyExist(): void
    {
        $customer = $this->customerRepository->findOneBy(['name' => 'customertest0']);
        $this->client->request('POST', '/api/customers', ["name"=>$customer->getName()]);
        $this->assertResponseStatusCodeSame(400);
        $response = $this->client->getResponse()->getContent();
        $this->assertJson($response);
    }
}
