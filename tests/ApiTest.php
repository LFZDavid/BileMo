<?php

namespace App\Tests;

use App\Entity\Supplier;
use App\DataFixtures\AppFixtures;
use App\Repository\ProductRepository;
use App\Repository\CustomerRepository;
use App\Repository\SupplierRepository;
use App\Service\PaginatedDataProvider;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\SerializerInterface;

class ApiTest extends WebTestCase
{

    use FixturesTrait;
    private KernelBrowser $client;
    private ProductRepository $productRepository;
    private CustomerRepository $customerRepository;
    private Supplier $supplierTest;
    private SupplierRepository $supplierRepository;
    private SerializerInterface $serializer;
    private array $expectedJson = [
        'testProductShow' => '{"id":%d,"name":"find","brand":"Test","stock":0,"price":0}',
        '404' => '{"status":404,"type":"about:blank","title":"Not Found"}',
    ];


    /**
     * This method is called before each test.
     */
    public function setUp(): void
    {
        $this->client = $this->createAuthenticatedClient();
        $this->loadFixtures([AppFixtures::class]);
        $this->productRepository = static::$container->get(ProductRepository::class);
        $this->customerRepository = static::$container->get(CustomerRepository::class);
        $this->supplierRepository = static::$container->get(SupplierRepository::class);
        $this->supplierTest = $this->supplierRepository->findOneBy(["name" => "SupplierTest"]);
        $this->serializer = static::$container->get(SerializerInterface::class);
    }

    protected function createAuthenticatedClient($username = 'SupplierTest', $password = 'pwdtest')
    {
        $client = static::createClient();
        $client->request(
        'POST',
        '/api/login_check',
        array(),
        array(),
        array('CONTENT_TYPE' => 'application/json'),
        json_encode(array(
            'username' => $username,
            'password' => $password,
            ))
        );

        $data = json_decode($client->getResponse()->getContent(), true);
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }

    public function testProductList(): void
    {
        $this->client->request('GET','/api/products');
        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testProductListFirstPage(): void
    {
        $this->client->request('GET','/api/products');
        $response = $this->client->getResponse()->getContent();
        $expectedJson = $this->serializer->serialize(PaginatedDataProvider::getData(
            $this->productRepository->getProductPaginator(), 
            1
        ), 'json',);
        $this->assertResponseIsSuccessful();
        $this->assertJson($response);
        $this->assertTrue($expectedJson === $response);
    }

    public function testProductListSecondPage(): void
    {
        $this->client->request('GET','/api/products', ['page' => 2]);
        $response = $this->client->getResponse()->getContent();
        $expectedJson = $this->serializer->serialize(PaginatedDataProvider::getData(
            $this->productRepository->getProductPaginator(2), 
            2
        ), 'json',);
        $this->assertResponseIsSuccessful();
        $this->assertJson($response);
        $this->assertTrue($expectedJson === $response);
    }

    public function testWrongProductListPaginator(): void
    {
        $this->client->request('GET','/api/products', ['page' => "x"]);
        
        $response = $this->client->getResponse()->getContent();
        $this->assertJson($response);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_NOT_FOUND);
        $this->assertResponseHeaderSame('Content-Type', 'application/problem+json');
        $this->assertTrue($this->expectedJson['404'] === $response);
    }
    
    public function testProductShow(): void
    {
        /** Get the test product installed with fixtures */
        $product = $this->productRepository->findOneBy(['name' => 'find']);
        $this->client->request('GET','/api/products/'.$product->getId());
        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse()->getContent();
        $this->assertJson($response);
        $this->assertTrue(sprintf($this->expectedJson['testProductShow'], $product->getId()) === $response);

    }

    public function testWrongProductShow(): void
    {
        $this->client->request('GET','/api/products/0');
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_NOT_FOUND);
        $response = $this->client->getResponse()->getContent();
        $this->assertJson($response);
        $this->assertResponseHeaderSame('Content-Type', 'application/problem+json');
        $this->assertTrue($this->expectedJson['404'] === $response);
    }

    public function testCustomerList(): void
    {
        $this->client->request('GET','/api/customers');
        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testCustomerListFirstPage(): void
    {
        $this->client->request('GET','/api/customers');
        $response = $this->client->getResponse()->getContent();
        $expectedJson = 
        $this->serializer->serialize(
            PaginatedDataProvider::getData(
            $this->customerRepository->getCustomerPaginator($this->supplierTest, 1), 
            1
        ), 
        'json',
        ['groups' => 'get_customers']);
        
        $this->assertResponseIsSuccessful();
        $this->assertJson($response);
        $this->assertTrue($expectedJson === $response);
    }

    public function testWrongCustomerListPaginator(): void
    {
        $this->client->request('GET','/api/customers', ['page' => "x"]);
        $response = $this->client->getResponse()->getContent();
        $expectedJson = 
        $this->serializer->serialize(
            $this->customerRepository->getCustomerPaginator($this->supplierTest), 'json',
            ['groups' => 'get_customers']
        );
        $this->assertJson($response);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_NOT_FOUND);
        $this->assertResponseHeaderSame('Content-Type', 'application/problem+json');
        $this->assertTrue($this->expectedJson['404'] === $response);
    }

    public function testCustomerShow(): void
    {
        $customer = $this->customerRepository->findOneBy(['name' => 'find']);
        $expectedJson = '{"id":'.$customer->getId().',"name":"'.$customer->getName().'"}';
        $this->client->request('GET', '/api/customers/'. $customer->getId());
        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse()->getContent();
        $this->assertJson($response);
        $this->assertTrue($expectedJson === $response);
    }

    public function testWrongCustomerShow(?int $id = 0): void 
    {
        $this->client->request('GET', '/api/customers/'.$id);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_NOT_FOUND);

        $response = $this->client->getResponse()->getContent();
        $this->assertJson($response);
        $this->assertResponseHeaderSame('Content-Type', 'application/problem+json');
    }

    public function testCreateCustomer(): void
    {
        $json = '{"name":"new Customer Test"}';
       
        $this->client->request('POST', '/api/customers', [], [], [], $json);
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
        $this->assertResponseHeaderSame('Content-Type', 'application/problem+json');
    }
    
    public function testCreateCustomerWithBlankName(): void
    {
        $this->client->request('POST', '/api/customers', ["name"=>""]);
        $this->assertResponseStatusCodeSame(400);
        $response = $this->client->getResponse()->getContent();
        $this->assertJson($response);
        $this->assertResponseHeaderSame('Content-Type', 'application/problem+json');
    }

    public function testCreateCustomerNameAlreadyExist(): void
    {
        $customer = $this->customerRepository->findOneBy(['name' => 'already_exist']);
        $json = '{"name":"'.$customer->getName().'"}';
        $this->client->request('POST', '/api/customers', [], [], [], $json);
        $this->assertResponseStatusCodeSame(400);
        $response = $this->client->getResponse()->getContent();
        $this->assertJson($response);
        $this->assertResponseHeaderSame('Content-Type', 'application/problem+json');
    }

    public function testDeleteCustomer():  void
    {
        $customer = $this->customerRepository->findOneBy(['name' => 'delete']);
        /**save id for verif after process */
        $customerId = $customer->getId();
        $this->client->request('DELETE', '/api/customers/'.$customer->getId());
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_NO_CONTENT);
        
        /**Test empty content response */
        $response = $this->client->getResponse()->getContent();
        $this->assertEmpty($response);

        /** Test if customer is deleted */
        $this->assertEquals(null, $this->customerRepository->find($customerId));

    }

    public function testDeleteOtherSuppliersCustomer(): void
    {
        $customer = $this->customerRepository->findOneBy(['name' => 'otherSupplierCustomer']);
        $this->client->request('DELETE', '/api/customers/'.$customer->getId());
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_FORBIDDEN);
        $this->assertResponseHeaderSame('Content-Type', 'application/problem+json');
    }

    public function testWrongCustomerDelete(?int $id = 0): void 
    {
        $this->client->request('DELETE', '/api/customers/'.$id);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_NOT_FOUND);
        $response = $this->client->getResponse()->getContent();
        $this->assertJson($response);
        $this->assertResponseHeaderSame('Content-Type', 'application/problem+json');
    }
}
