<?php

namespace App\Controller;

use App\Entity\Product;
use App\Pagination\PaginationFactory;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

/**
 * @Cache(maxage="3600", public=true)
 * @SWG\Response(
 *      response=404,
 *      description="Not Found"
 * )
 * @Security(name="Bearer")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/api/products", name="get_products", methods={"GET"})
     * List of products (paginated)
     * @SWG\Response(
     *      response=200,
     *      description="Returns the paginated list of products.",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref=@Model(type=Product::class))    
     *      )
     * )
     * @SWG\Parameter(
     *      name="page",
     *      in="query",
     *      type="integer",
     *      description="The page number to display"
     * )
     * @SWG\Parameter(
     *      name="brand",
     *      in="query",
     *      type="string",
     *      description="Filter can be use to search product by brand"
     * )
     * @SWG\Tag(name="Product")
     */
    public function productList(Request $request, ProductRepository $productRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $brand = $request->query->get('brand');

        return $this->json(
            (new PaginationFactory($this->container->get('router')))
            ->createCollection(
                $page,
                $productRepository->getProductPaginator($page, $brand), 
                'get_products',
            )
        );
    }

    /**
     * @Route("/api/products/{id}",  name="get_product", methods={"GET"})
     * @SWG\Response(
     *      response=200,
     *      description="Returns details of a product.",
     *      @SWG\Schema(ref=@Model(type=Product::class))
     * )
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      type="integer",
     *      description="Unique product's id"
     * )
     * @SWG\Tag(name="Product")
     */
    public function productShow(Product $product): Response
    {
        return $this->json($product);
    }
}
