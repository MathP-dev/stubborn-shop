<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findFeaturedProducts(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.featured = :featured')
            ->setParameter('featured', true)
            ->getQuery()
            ->getResult();
    }

    public function findByPriceRange(? float $minPrice, ?float $maxPrice): array
    {
        $qb = $this->createQueryBuilder('p');

        if ($minPrice !== null) {
            $qb->andWhere('p.price >= :minPrice')
               ->setParameter('minPrice', $minPrice);
        }

        if ($maxPrice !== null) {
            $qb->andWhere('p.price <= :maxPrice')
               ->setParameter('maxPrice', $maxPrice);
        }

        return $qb->getQuery()->getResult();
    }
}
