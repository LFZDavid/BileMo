<?php

namespace App\Pagination;

use App\ApiProblem;
use App\Exception\ApiProblemException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

class PaginationFactory
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function createCollection(int $page, Paginator $paginator, string $route, array $routeParams = [])
    {
        if($page < 1) {
            throw new ApiProblemException(
                new ApiProblem(JsonResponse::HTTP_NOT_FOUND)
            );
        }
        $paginatedCollection = new PaginatedCollection($paginator);
        $createLinkUrl = function($targetPage) use ($route, $routeParams) {
            return $this->router->generate($route, array_merge($routeParams, ['page' => $targetPage]));
        };

        $paginatedCollection->addLink('first', $createLinkUrl(1));

        if($paginatedCollection->hasPrevPage($page))
            $paginatedCollection->addLink('prev', $createLinkUrl($page - 1));
        
        $paginatedCollection->addLink('self', $createLinkUrl($page));
        
        if($paginatedCollection->hasNextPage($page))
            $paginatedCollection->addLink('next', $createLinkUrl($page + 1));

        $paginatedCollection->addLink('last', $createLinkUrl($paginatedCollection->getNbPages()));

        return $paginatedCollection;
    }
}