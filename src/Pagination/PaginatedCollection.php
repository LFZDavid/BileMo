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

    public function __construct (Paginator $paginator)
    {
        $this->paginator = $paginator;
        $this->total = $paginator->count();
        $count =  $paginator->getIterator()->count();
        
        if($count < 1) {
            throw new ApiProblemException(
                new ApiProblem(JsonResponse::HTTP_NOT_FOUND)
            );
        }
        $this->count = $count;
    }
}