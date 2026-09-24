<?php

namespace App\Repository;

use App\Entity\BloodSugar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
/**
 * @extends ServiceEntityRepository<BloodSugar>
 */
class BloodSugarRepository extends ServiceEntityRepository
{
    
    /**
 * Retourne les mesures de glycémie d'un patient pour une journée donnée,
 * triées chronologiquement.
 *
 * @return BloodSugar[]
 */
public function findForPatientAndDate(User $patient, \DateTimeInterface $date): array
{
    $start = \DateTimeImmutable::createFromInterface($date)->setTime(0, 0, 0);
    $end = $start->modify('+1 day');

    return $this->createQueryBuilder('b')
        ->andWhere('b.patient = :patient')
        ->andWhere('b.date >= :start')
        ->andWhere('b.date < :end')
        ->setParameter('patient', $patient)
        ->setParameter('start', $start)
        ->setParameter('end', $end)
        ->orderBy('b.time', 'ASC')
        ->getQuery()
        ->getResult();
}

/**
 * Retourne les mesures de glycémie d'un patient
 * comprises entre deux dates.
 *
 * @return BloodSugar[]
 */
public function findForPatientAndPeriod(
    User $patient,
    \DateTimeInterface $startDate,
    \DateTimeInterface $endDate
): array {
    $start = \DateTimeImmutable::createFromInterface($startDate)
        ->setTime(0, 0, 0);

    $end = \DateTimeImmutable::createFromInterface($endDate)
        ->setTime(23, 59, 59);

    return $this->createQueryBuilder('b')
        ->andWhere('b.patient = :patient')
        ->andWhere('b.date >= :start')
        ->andWhere('b.date <= :end')
        ->setParameter('patient', $patient)
        ->setParameter('start', $start)
        ->setParameter('end', $end)
        ->orderBy('b.date', 'ASC')
        ->addOrderBy('b.time', 'ASC')
        ->getQuery()
        ->getResult();
}
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BloodSugar::class);
    }

    //    /**
    //     * @return BloodSugar[] Returns an array of BloodSugar objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?BloodSugar
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
