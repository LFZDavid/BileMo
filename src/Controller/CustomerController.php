<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use App\Repository\SupplierRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class CustomerController extends AbstractController
{
    /**
     * @Route("/api/customers", name="get_customers", methods={"GET"})
     */
    public function customerList(CustomerRepository $customerRepository, SupplierRepository $supplierRepository): Response
    {
        /** Replace it when auth wil be implemented */
        $supplier = $supplierRepository->findOneBy(["name" => "Orange"]);

        return $this->json($customerRepository->findBy(["supplier" => $supplier->getId()]), 200, [], ['groups' => 'get_customers']);
    }

    /**
     * @Route("/api/customers/{id}", name="get_customer", methods={"GET"})
     */
    public function customerShow(Customer $customer, SerializerInterface $serializer):Response
    {
        $serializedCustomer = $serializer->serialize($customer, 'json', ['groups' => 'get_customers']);

        return new JsonResponse($serializedCustomer, 200, [], true);
    }
}
