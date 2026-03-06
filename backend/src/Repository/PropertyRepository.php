<?php

namespace App\Repository;

use App\Entity\Property;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Property>
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    /**
     * @return Property[]
     */
    public function findByOwner(User $owner): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.owner = :owner')
            ->setParameter('owner', $owner)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Property[]
     */
    public function findByOwnerAndStatus(User $owner, string $status): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.owner = :owner')
            ->andWhere('p.status = :status')
            ->setParameter('owner', $owner)
            ->setParameter('status', $status)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function countByOwnerAndStatus(User $owner, string $status): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.owner = :owner')
            ->andWhere('p.status = :status')
            ->setParameter('owner', $owner)
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
