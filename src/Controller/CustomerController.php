<?php

namespace App\Controller;

use App\Entity\Supplier;
use App\Repository\CustomerRepository;
use App\Repository\SupplierRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CustomerController extends AbstractController
{
    /**
     * @Route("/api/customers", name="get_customers", methods={"GET"})
     */
    public function customerList(CustomerRepository $customerRepository, SupplierRepository $supplierRepository): Response
    {
        /** Replace it when auth wil be implemented */
        $supplier = $supplierRepository->findOneBy(["name" => "Orange"]);

        return $this->json($customerRepository->findBy(["supplier" => $supplier->getId()]), 200, [], ["groups" => "customer_list"]);
    }
}
