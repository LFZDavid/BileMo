<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Supplier;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }
    
    public function getCustomerPaginator(Supplier $supplier, int $page = 1):Paginator
    {
        $offset = $page ? (($page - 1) * self::PAGINATOR_PER_PAGE) : 0;
        $query = $this->createQueryBuilder('c')
            ->andWhere('c.supplier = :supplier')
            ->setParameter('supplier', $supplier)
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery();
        return new Paginator($query);
    }
}
