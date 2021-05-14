<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/api/products", name="products", methods={"GET"})
     */
    public function productList(ProductRepository $productRepository): Response
    {
        return $this->json($productRepository->findAll());
    }
}
