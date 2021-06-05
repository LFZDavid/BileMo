<?php

namespace App\Service;

use App\ApiProblem;
use App\Exception\ApiProblemException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\JsonResponse;

class PaginatedDataProvider
{
    public static function getData(Paginator $paginator, int $page): array
    {
        $count =  $paginator->getIterator()->count();
        
        if($page < 1 || $count < 1) {
            throw new ApiProblemException(
                new ApiProblem(JsonResponse::HTTP_NOT_FOUND)
            );
        }

        return [
            'total' => $paginator->count(),
            'count' => $count,
            'products' => $paginator,
        ];
    }
}