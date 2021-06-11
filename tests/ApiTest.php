<?php

namespace App\Tests;

include 'ressources/expectedJson.php';

use App\Entity\Supplier;
use App\DataFixtures\AppFixtures;
use App\Pagination\PaginationFactory;
use App\Repository\ProductRepository;
use App\Repository\CustomerRepository;
use App\Repository\SupplierRepository;
use App\Pagination\PaginatedCollection;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouterInterface;
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
    private RouterInterface $router;
    private array $expectedJson;


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
        $this->router = static::$container->get('router');
        $this->expectedJson = EXPECTED_JSON;

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

    public function testProductListFirstPage(): void
    {
        $this->client->request('GET','/api/products');
        $response = $this->client->getResponse()->getContent();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response);
        $this->assertTrue($this->expectedJson['testProductListFirstPage'] === $response);
    }

    public function testProductListSecondPage(): void
    {
        $this->client->request('GET','/api/products', ['page' => 2]);
        $response = $this->client->getResponse()->getContent();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response);
        $this->assertTrue($this->expectedJson['testProductListSecondPage'] === $response);
    }

    public function testProductListWithBrandFilter(): void
    {
        $this->client->request('GET', '/api/products', ['brand' => 'Apple']);
        $response = $this->client->getResponse()->getContent();
        $this->assertJson($response);
        $this->assertTrue($this->expectedJson['testProductListWithBrandFilter'] === $response);
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
        $response = $this->client->getResponse()->getContent();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response);
        $this->assertTrue($this->expectedJson['testCustomerList'] === $response);
    }

    public function testCustomerListFirstPage(): void
    {
        $this->client->request('GET','/api/customers');
        $response = $this->client->getResponse()->getContent();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response);
        $this->assertTrue($this->expectedJson['testCustomerListFirstPage'] === $response);
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
        $this->client->request('GET', '/api/customers/'. $customer->getId());
        $response = $this->client->getResponse()->getContent();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response);
        $this->assertTrue($this->expectedJson['testCustomerShow'] === $response);
    }

    public function testWrongCustomerShow(?int $id = 0): void 
    {
        $this->client->request('GET', '/api/customers/'.$id);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_NOT_FOUND);

        $response = $this->client->getResponse()->getContent();
        $this->assertJson($response);
        $this->assertResponseHeaderSame('Content-Type', 'application/problem+json');
        $this->assertTrue($this->expectedJson['testWrongCustomerShow'] === $response);
    }

    public function testCreateCustomer(): void
    {
        $json = '{"name":"new Customer Test"}';
       
        $this->client->request('POST', '/api/customers', [], [], [], $json);
        $response = $this->client->getResponse()->getContent();
        $this->assertResponseStatusCodeSame(201);
        $this->assertJson($response);
        $this->assertTrue($this->expectedJson['testCreateCustomer'] === $response);
    }
    
    public function testCreateCustomerWithoutData(): void
    {
        $this->client->request('POST', '/api/customers', []);
        $this->assertResponseStatusCodeSame(400);
        $response = $this->client->getResponse()->getContent();
        $this->assertJson($response);
        $this->assertResponseHeaderSame('Content-Type', 'application/problem+json');
        $this->assertTrue($this->expectedJson['testCreateCustomerWithoutData'] === $response);
    }
    
    public function testCreateCustomerWithBlankName(): void
    {
        $this->client->request('POST', '/api/customers', ["name"=>""]);
        $this->assertResponseStatusCodeSame(400);
        $response = $this->client->getResponse()->getContent();
        $this->assertJson($response);
        $this->assertResponseHeaderSame('Content-Type', 'application/problem+json');
        $this->assertTrue($this->expectedJson['testCreateCustomerWithBlankName'] === $response);
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
        $this->assertTrue($this->expectedJson['testCreateCustomerNameAlreadyExist'] === $response);
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
        $response = $this->client->getResponse()->getContent();
        $this->assertJson($response);
        $this->assertTrue($this->expectedJson['testDeleteOtherSuppliersCustomer'] === $response);
    }

    public function testWrongCustomerDelete(?int $id = 0): void 
    {
        $this->client->request('DELETE', '/api/customers/'.$id);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_NOT_FOUND);
        $response = $this->client->getResponse()->getContent();
        $this->assertJson($response);
        $this->assertResponseHeaderSame('Content-Type', 'application/problem+json');
        $this->assertTrue($this->expectedJson['testWrongCustomerDelete'] === $response);
    }
}
