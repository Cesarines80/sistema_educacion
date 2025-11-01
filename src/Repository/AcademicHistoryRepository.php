<?php

namespace App\Repository;

use App\Entity\AcademicHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AcademicHistory>
 *
 * @method AcademicHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method AcademicHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method AcademicHistory[]    findAll()
 * @method AcademicHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AcademicHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AcademicHistory::class);
    }
}
