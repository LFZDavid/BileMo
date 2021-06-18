<?php

namespace App\Controller;

use App\ApiProblem;
use App\Entity\Customer;
use App\Pagination\PaginationFactory;
use App\Exception\ApiProblemException;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

class CustomerController extends AbstractController
{
    /**
     * @Route("/api/customers", name="get_customers", methods={"GET"})
     * @SWG\Response(
     *      response=200,
     *      description="Returns list of supplier's customers",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref=@Model(type=Customer::class, groups={"get_customers"}))
     *      )
     * )
     * @SWG\Parameter(
     *      name="page",
     *      in="path",
     *      type="integer",
     *      description="The page number to display"
     * )
     * @SWG\Parameter(
     *      name="name",
     *      in="path",
     *      type="string",
     *      description="Filter can be use to search customer by name"
     * )
     * @SWG\Tag(name="customers")
     * @Security(name="Bearer")
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
     *      description="Returns details of a customer."
     * )
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      type="integer",
     *      description="Unique customer's id"
     * )
     * @SWG\Tag(name="customer")
     * @Security(name="Bearer")
     */
    public function show(Customer $customer, SerializerInterface $serializer):Response
    {
        $this->denyAccessUnlessGranted('view', $customer,'Vous ne pouvez pas accéder à ce client!');

        $serializedCustomer = $serializer->serialize($customer, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['supplier']]);
        
        return new JsonResponse($serializedCustomer, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/customers", name="create_customer", methods={"POST"})
     */
    public function create(
        Request $request, 
        EntityManagerInterface $manager, 
        UrlGeneratorInterface $urlGenerator, 
        ValidatorInterface $validator,
        SerializerInterface $serializer
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

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($customer);
        if($errors->count() > 0){
            foreach ($errors->getIterator()->getArrayCopy() as $constraintViolation)
                /** @var ConstraintViolation  $constraintViolation */
                $errorMessages[$constraintViolation->getPropertyPath()] = $constraintViolation->getMessage();
            $apiProblem = new ApiProblem(JsonResponse::HTTP_BAD_REQUEST, ApiProblem::TYPE_VALIDATION_ERROR);
            $apiProblem->set('errors', $errorMessages, 'json');
            throw new ApiProblemException($apiProblem);
        }

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
     * @Route("/api/customers/{id}", name="delete_customer", methods={"DELETE"})
     */
    public function delete(Customer $customer, EntityManagerInterface $manager)
    {
        $this->denyAccessUnlessGranted('delete', $customer,'Vous n\'êtes pas authorisé à supprimer ce client!');

        $manager->remove($customer);
        $manager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

}
