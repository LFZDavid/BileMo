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
    public function list(CustomerRepository $customerRepository, SupplierRepository $supplierRepository): Response
    {
        /** Replace it when auth wil be implemented */
        $supplier = $this->getSupplier($supplierRepository);

        return $this->json($customerRepository->findBy(["supplier" => $supplier->getId()]), JsonResponse::HTTP_OK, [], ['groups' => 'get_customers']);
    }

    /**
     * @Route("/api/customers/{id}", name="get_customer", methods={"GET"})
     */
    public function show(?Customer $customer, SerializerInterface $serializer):Response
    {
        if(!$customer){
            return $this->json(['message' => 'Resource introuvable'], JsonResponse::HTTP_NOT_FOUND);
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
        SupplierRepository $supplierRepository,
        ValidatorInterface $validator
        ):Response
    {
        /** Replace it when auth wil be implemented */
        $supplier = $this->getSupplier($supplierRepository);

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
    public function delete(?Customer $customer, EntityManagerInterface $manager, SupplierRepository $supplierRepository)
    {
        /** Replace it when auth wil be implemented */
        $supplier = $this->getSupplier($supplierRepository);

        if(!$customer){
            return $this->json(['message' => 'Resource introuvable'], JsonResponse::HTTP_NOT_FOUND);
        }

        if($customer->getSupplier()->getId() !== $supplier->getId()){
            return $this->json(['message' => 'Vous n\'êtes pas authorisé à supprimer ce client!'], JsonResponse::HTTP_FORBIDDEN);
        }

        $manager->remove($customer);
        $manager->flush();

        return $this->json(['message' => 'Le client à été supprimé'], JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     *  Will be replace when auth wil be implemented 
     * */
    public function getSupplier(SupplierRepository $supplierRepository):Supplier
    {
        return $supplierRepository->findOneBy(["name" => "SupplierTest"]);
    }
}
