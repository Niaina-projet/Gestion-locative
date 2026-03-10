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
                'SELECT * FROM property ORDER BY COALESCE(updated_at, created_at) DESC',
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
                'SELECT * FROM property WHERE owner_id = :owner ORDER BY COALESCE(updated_at, created_at) DESC',
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
                'SELECT * FROM property WHERE owner_id = :owner AND status = :status ORDER BY COALESCE(updated_at, created_at) DESC',
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

    /**
     * @return array{data: Property[], total: int}
     */
    public function findWithFilters(
        ?User $owner,
        ?string $type,
        ?string $status,
        ?string $city,
        ?string $search,
        int $page,
        int $limit,
    ): array {
        ['conditions' => $conditions, 'params' => $params] = $this->buildFilterConditions(
            $owner, $type, $status, $city, $search
        );

        $whereClause = implode(' AND ', $conditions);
        $offset = ($page - 1) * $limit;

        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(Property::class, 'p');

        $countRsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $countRsm->addScalarResult('total', 'total');

        $countQuery = $this->getEntityManager()
            ->createNativeQuery("SELECT COUNT(*) as total FROM property WHERE {$whereClause}", $countRsm);

        $dataQuery = $this->getEntityManager()
            ->createNativeQuery(
                "SELECT * FROM property WHERE {$whereClause} ORDER BY COALESCE(updated_at, created_at) DESC LIMIT :limit OFFSET :offset",
                $rsm
            );

        foreach ($params as $key => $value) {
            $countQuery->setParameter($key, $value);
            $dataQuery->setParameter($key, $value);
        }

        $dataQuery->setParameter('limit', $limit);
        $dataQuery->setParameter('offset', $offset);

        return [
            'data' => $dataQuery->getResult(),
            'total' => (int) $countQuery->getSingleScalarResult(),
        ];
    }

    /**
     * @return array{conditions: string[], params: array<string, mixed>}
     */
    private function buildFilterConditions(
        ?User $owner,
        ?string $type,
        ?string $status,
        ?string $city,
        ?string $search,
    ): array {
        $filters = [
            'owner_id = :owner' => ['owner', $owner?->getId()],
            'type = :type' => ['type', $type],
            'status = :status' => ['status', $status],
            'city LIKE :city' => ['city', $city ? '%'.$city.'%' : null],
            '(title LIKE :search OR address LIKE :search)' => ['search', $search ? '%'.$search.'%' : null],
        ];

        $conditions = ['1=1'];
        $params = [];

        foreach ($filters as $condition => [$paramName, $value]) {
            if (null !== $value) {
                $conditions[] = $condition;
                $params[$paramName] = $value;
            }
        }

        return ['conditions' => $conditions, 'params' => $params];
    }
}
