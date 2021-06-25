<?php

namespace App\Controller;

use App\ApiProblem;
use App\Entity\Customer;
use App\Pagination\PaginationFactory;
use App\Exception\ApiProblemException;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\EntityChecker;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

/**
 * @SWG\Parameter(
 *     name="Authorization",
 *     required= true,
 *     in="header",
 *     type="string",
 *     description="Bearer Token",
 * )
 *
 * @SWG\Response(
 *      response="401",
 *      description="Unauthorized: Expired JWT Token or JWT Token not found",
 * )
 * @SWG\Response(
 *      response="403",
 *      description="Unauthorized: You're not allowed to access to that ressource",
 * )
 * @Security(name="Bearer")
 */
class CustomerController extends AbstractController
{
    /**
     * @Route("/api/customers", name="get_customers", methods={"GET"})
     * @SWG\Response(
     *      response=200,
     *      description="Returns list of supplier's customers",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref=@Model(type=Customer::class))
     *      )
     * )
     * @SWG\Parameter(
     *      name="page",
     *      in="query",
     *      type="integer",
     *      description="The page number to display"
     * )
     * @SWG\Parameter(
     *      name="name",
     *      in="query",
     *      type="string",
     *      description="Filter can be use to search customer by name"
     * )
     * @SWG\Tag(name="Customer")
     */
    public function list(Request $request, CustomerRepository $customerRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $name = $request->query->get('name');
        $paginationFactory = new PaginationFactory($this->container->get('router'));
        $data = $paginationFactory->createCollection(
                $page,
                $customerRepository->getCustomerPaginator($this->getUser(), $page, $name), 
                'get_customers'
            );

        return $this->json($data, JsonResponse::HTTP_OK, [], [AbstractNormalizer::IGNORED_ATTRIBUTES => ['supplier']]);
    }

    /**
     * @Route("/api/customers/{id}", name="get_customer", methods={"GET"})
     * @SWG\Response(
     *      response=200,
     *      description="Returns details of a customer.",
     *      @SWG\Schema(ref=@Model(type=Customer::class))
     * )
     
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      type="integer",
     *      description="Unique customer's id"
     * )
     * @SWG\Tag(name="Customer")
     */
    public function show(Customer $customer, SerializerInterface $serializer):Response
    {
        $this->denyAccessUnlessGranted('view', $customer,'Vous ne pouvez pas accéder à ce client!');

        $serializedCustomer = $serializer->serialize($customer, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['supplier']]);
        
        return new JsonResponse($serializedCustomer, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/customers", name="create_customer", methods={"POST"})
     * @SWG\Response(
     *      response=201,
     *      description="Returns created customer",
     *      @SWG\Schema(ref=@Model(type=Customer::class))
     * )
     * @SWG\Response(
     *      response=400,
     *      description="Validation error : 'customer's name unavailable!'"
     * )
     * @SWG\Parameter(
     *      name="name",
     *      in="body",
     *      type="string",
     *      @SWG\Schema(@SWG\Property(property="name", type="string", example="John Doe")),
     *      description="Customer's name (must be unique)"
     * )
     * @SWG\Tag(name="Customer")
     */
    public function create(
        Request $request, 
        EntityManagerInterface $manager, 
        UrlGeneratorInterface $urlGenerator, 
        SerializerInterface $serializer,
        EntityChecker $checker
        ):Response
    {
        try {
            $customer = $serializer->deserialize($request->getContent(), Customer::class,'json');
        } catch (\Throwable $th) {
            
            throw new ApiProblemException(
                new ApiProblem(
                    JsonResponse::HTTP_BAD_REQUEST, ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT
                    )
            );
            
        }

        $supplier = $this->getUser()->addCustomer($customer);

        $checker->check($customer);
        $manager->persist($supplier);
        $manager->flush();


        return $this->json(
            $customer, 
            JsonResponse::HTTP_CREATED, 
            [
                'Location' => $urlGenerator->generate(
                    "get_customer", 
                    ["id" => $customer->getId()]
                )
            ],
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['supplier']]
        );
        
    }

    /**
     * @Route("/api/customers/{id}", name="update_customer", methods={"PUT"})
     * @SWG\Response(
     *      response=200,
     *      description="Returns updated customer",
     *      @SWG\Schema(ref=@Model(type=Customer::class))
     * )
     * @SWG\Response(
     *      response=400,
     *      description="Validation error : 'customer's name unavailable!'"
     * )
     * @SWG\Parameter(
     *      name="name",
     *      in="body",
     *      type="string",
     *      @SWG\Schema(@SWG\Property(property="name", type="string", example="John Doe")),
     * )
     * @SWG\Tag(name="Customer")
     */
    public function update(
    Request $request,
    Customer $customer, 
    EntityManagerInterface $manager, 
    UrlGeneratorInterface $urlGenerator, 
    EntityChecker $checker,
    SerializerInterface $serializer
    ):Response
    {
        $this->denyAccessUnlessGranted('edit', $customer,'Vous ne pouvez pas accéder à ce client!');

        try {
            $updateCustomer = $serializer->deserialize($request->getContent(), Customer::class,'json');
        } catch (\Throwable $th) {
            throw new ApiProblemException(
                new ApiProblem(
                    JsonResponse::HTTP_BAD_REQUEST, ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT
                    )
            );
        }
        
        $customer->setName($updateCustomer->getName());

        $checker->check($customer);

        $manager->persist($customer);
        $manager->flush();
        return $this->json(
            $customer, 
            JsonResponse::HTTP_OK, 
            [
                'Location' => $urlGenerator->generate(
                    "get_customer", 
                    ["id" => $customer->getId()]
                )
            ],
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['supplier']]
        );
    }

    /**
     * @Route("/api/customers/{id}", name="delete_customer", methods={"DELETE"})
     * @SWG\Response(
     *      response=204,
     *      description="Successful operation"
     * )
     * @SWG\Tag(name="Customer")
     */
    public function delete(Customer $customer, EntityManagerInterface $manager)
    {
        $this->denyAccessUnlessGranted('delete', $customer,'Vous n\'êtes pas authorisé à supprimer ce client!');

        $manager->remove($customer);
        $manager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

}
