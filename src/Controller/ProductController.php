<?php

namespace App\Controller;

use App\ApiProblem;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Exception\ApiProblemException;
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
        if($page < 1) {
            // todo : test if request page !> last_page
            throw new ApiProblemException(
                new ApiProblem(JsonResponse::HTTP_NOT_FOUND)
            );
                
        }
        return $this->json($productRepository->getProductPaginator($page));
    }

    /**
     * @Route("/api/products/{id}",  name="get_product", methods={"GET"})
     */
    public function productShow(Product $product): Response
    {
        return $this->json($product);
    }
}
