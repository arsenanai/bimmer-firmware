<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SoftwareVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SoftwareVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SoftwareVersion::class);
    }

    /**
     * @return SoftwareVersion[]
     */
    public function findBySystemVersionAlt(string $versionAlt): array
    {
        return $this->createQueryBuilder('sv')
            ->where('LOWER(sv.systemVersionAlt) = LOWER(:version)')
            ->setParameter('version', $versionAlt)
            ->getQuery()
            ->getResult();
    }

    public function findLatestDisplayVersion(bool $isLCI): ?string
    {
        $qb = $this->createQueryBuilder('sv')
            ->select('sv.latestDisplayVersion')
            ->where('sv.isLatest = true')
            ->andWhere('sv.latestDisplayVersion IS NOT NULL');

        if ($isLCI) {
            $qb->andWhere('sv.name LIKE :prefix')->setParameter('prefix', 'LCI%');
        } else {
            $qb->andWhere('sv.name NOT LIKE :prefix')->setParameter('prefix', 'LCI%');
        }

        $result = $qb->setMaxResults(1)->getQuery()->getOneOrNullResult();

        return $result ? $result['latestDisplayVersion'] : null;
    }
}
