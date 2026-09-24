<?php

namespace App\Repository;

use App\Entity\Meal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Meal>
 */
class MealRepository extends ServiceEntityRepository
{
    
    /**
 * Retourne les repas d'un patient pour une journée donnée,
 * triés chronologiquement.
 *
 * @return Meal[]
 */
public function findForPatientAndDate(User $patient, \DateTimeInterface $date): array
{
    $start = \DateTimeImmutable::createFromInterface($date)->setTime(0, 0, 0);
    $end = $start->modify('+1 day');

    return $this->createQueryBuilder('m')
        ->andWhere('m.patient = :patient')
        ->andWhere('m.date >= :start')
        ->andWhere('m.date < :end')
        ->setParameter('patient', $patient)
        ->setParameter('start', $start)
        ->setParameter('end', $end)
        ->orderBy('m.hour', 'ASC')
        ->getQuery()
        ->getResult();
}
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meal::class);
    }

//    /**
//     * @return Meal[] Returns an array of Meal objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Meal
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
