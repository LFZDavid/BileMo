<?php

namespace App\Tests;

use App\Entity\Supplier;
use App\DataFixtures\AppFixtures;
use App\Repository\ProductRepository;
use App\Repository\CustomerRepository;
use App\Repository\SupplierRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTest extends WebTestCase
{

    use FixturesTrait;
    private KernelBrowser $client;
    private ProductRepository $productRepository;
    private CustomerRepository $customerRepository;
    private Supplier $supplierTest;
    private SupplierRepository $supplierRepository;

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
        $customer = $this->customerRepository->findOneBy(['name' => 'already_exist']);
        $this->client->request('POST', '/api/customers', ["name"=>$customer->getName()]);
        $this->assertResponseStatusCodeSame(400);
        $response = $this->client->getResponse()->getContent();
        $this->assertJson($response);
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
    }

    public function testWrongCustomerDelete(?int $id = 0): void 
    {
        $this->client->request('DELETE', '/api/customers/'.$id);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_NOT_FOUND);
        $response = $this->client->getResponse()->getContent();
        $this->assertJson($response);
    }
}
