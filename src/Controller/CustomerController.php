<?php

namespace App\Controller;

use App\ApiProblem;
use App\Entity\Customer;
use App\Security\Voter\CustomerVoter;
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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CustomerController extends AbstractController
{
    /**
     * @Route("/api/customers", name="get_customers", methods={"GET"})
     */
    public function list(CustomerRepository $customerRepository): Response
    {
        return $this->json($customerRepository->findBy(["supplier" => $this->getUser()->getId()]), JsonResponse::HTTP_OK, [], ['groups' => 'get_customers']);
    }

    /**
     * @Route("/api/customers/{id}", name="get_customer", methods={"GET"})
     */
    public function show(Customer $customer, SerializerInterface $serializer):Response
    {
        $this->denyAccessUnlessGranted('view', $customer,'Vous ne pouvez pas accéder à ce client!');

        $serializedCustomer = $serializer->serialize($customer, 'json', ['groups' => 'get_customers']);
        
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
                $errorMessages[] = $constraintViolation->getMessage();
            $apiProblem = new ApiProblem(JsonResponse::HTTP_BAD_REQUEST, ApiProblem::TYPE_VALIDATION_ERROR);
            $apiProblem->set('errors', $serializer->serialize($errorMessages, 'json'));
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
            ['groups' => 'get_customers']
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
