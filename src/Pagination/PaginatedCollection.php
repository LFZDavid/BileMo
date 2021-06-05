<?php

namespace App\Pagination;

use App\ApiProblem;
use App\Exception\ApiProblemException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\JsonResponse;

class PaginatedCollection
{
    private Paginator $items;
    private int $total;
    private int $count;
    private array $_links = [];

    public function __construct (Paginator $paginator)
    {
        $this->items = $paginator;
        $this->total = $paginator->count();
        $count =  $paginator->getIterator()->count();
        
        if($count < 1) {
            throw new ApiProblemException(
                new ApiProblem(JsonResponse::HTTP_NOT_FOUND)
            );
        }
        $this->count = $count;
    }

    public function getItems(): Paginator
    {
        return $this->items;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getLinks(): array
    {
        return $this->_links;
    }

    public function addLink(string $ref, string $url): void
    {
        $this->_links[$ref] = $url;
    }

    public function getNbPages():int
    {
        return ceil(($this->total / $this->items->getQuery()->getMaxResults()));
    }

    public function hasNextPage(int $current):bool
    {
        return $current < $this->getNbPages();    
    }

    public function hasPrevPage(int $current):bool
    {
        return $current > 1;    
    }

}