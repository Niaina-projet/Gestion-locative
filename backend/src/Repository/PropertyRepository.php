<?php

namespace App\Repository;

use App\Entity\Property;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
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
    public function findAllOrderedByDate(): array
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(Property::class, 'p');

        return $this->getEntityManager()
            ->createNativeQuery(
                'SELECT * FROM property ORDER BY updated_at DESC NULLS LAST, created_at DESC',
                $rsm
            )
            ->getResult()
        ;
    }

    /**
     * @return Property[]
     */
    public function findByOwner(User $owner): array
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(Property::class, 'p');

        return $this->getEntityManager()
            ->createNativeQuery(
                'SELECT * FROM property WHERE owner_id = :owner ORDER BY updated_at DESC NULLS LAST, created_at DESC',
                $rsm
            )
            ->setParameter('owner', $owner->getId())
            ->getResult()
        ;
    }

    /**
     * @return Property[]
     */
    public function findByOwnerAndStatus(User $owner, string $status): array
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(Property::class, 'p');

        return $this->getEntityManager()
            ->createNativeQuery(
                'SELECT * FROM property WHERE owner_id = :owner AND status = :status ORDER BY updated_at DESC NULLS LAST, created_at DESC',
                $rsm
            )
            ->setParameter('owner', $owner->getId())
            ->setParameter('status', $status)
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
