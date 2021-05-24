<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Supplier;
use App\Repository\CustomerRepository;
use App\Repository\SupplierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
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
    public function show(?Customer $customer, SerializerInterface $serializer):Response
    {
        if(!$customer){
            return $this->json(['message' => 'Resource introuvable'], JsonResponse::HTTP_NOT_FOUND);
        }

        if($customer->getSupplier()->getId() !== $this->getUser()->getId()){
            return $this->json(['message' => 'Vous ne pouvez pas accéder à ce client.'], JsonResponse::HTTP_FORBIDDEN);
        }

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
        ValidatorInterface $validator
        ):Response
    {
        $supplier = $this->getUser();
        $customer = new Customer();
        $customer->setName($request->get('name'));
        $supplier->addCustomer($customer);

        $errors = $validator->validate($customer);
        if($errors->count() > 0){
            return $this->json($errors, JsonResponse::HTTP_BAD_REQUEST);
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
    public function delete(?Customer $customer, EntityManagerInterface $manager)
    {
        if(!$customer){
            return $this->json(['message' => 'Resource introuvable'], JsonResponse::HTTP_NOT_FOUND);
        }

        if($customer->getSupplier()->getId() !== $this->getUser()->getId()){
            return $this->json(['message' => 'Vous n\'êtes pas authorisé à supprimer ce client!'], JsonResponse::HTTP_FORBIDDEN);
        }

        $manager->remove($customer);
        $manager->flush();

        return $this->json(['message' => 'Le client à été supprimé'], JsonResponse::HTTP_NO_CONTENT);
    }

}
