<?php

namespace App\Pagination;

use App\ApiProblem;
use App\Exception\ApiProblemException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\JsonResponse;

class PaginatedCollection
{
    private Paginator $paginator;
    private array $items;
    private int $total;
    private int $count;
    private array $_links = [];

    public function __construct (Paginator $paginator, array $items, int $total)
    {
        $this->paginator = $paginator;
        $this->items = $items;
        $this->total = $total;
        $this->count = count($items);
    }

    public function getItems(): array
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
        return ceil(($this->total / $this->paginator->getQuery()->getMaxResults()));
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