<?php

namespace App\Controller;

use App\ApiProblem;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Exception\ApiProblemException;
use App\Service\PaginatedDataProvider;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/api/products", name="get_products", methods={"GET"})
     */
    public function productList(Request $request, ProductRepository $productRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        return $this->json(
            PaginatedDataProvider::getData(
                $productRepository->getProductPaginator($page), 
                $page
            )
        );
    }

    /**
     * @Route("/api/products/{id}",  name="get_product", methods={"GET"})
     */
    public function productShow(Product $product): Response
    {
        return $this->json($product);
    }
}
