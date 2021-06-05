<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function getProductPaginator(int $page = 1):Paginator
    {
        $offset = $page ? (($page - 1) * self::PAGINATOR_PER_PAGE) : 0;
        $query = $this->createQueryBuilder('p')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery();
        return new Paginator($query);
    }
}
