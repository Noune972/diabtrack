<?php

namespace App\Repository;

use App\Entity\SportingActivity;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SportingActivity>
 */
class SportingActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SportingActivity::class);
    }

    /**
     * Retourne les activités sportives d'un patient donné,
     * triées de la plus récente à la plus ancienne (date puis heure).
     *
     * @return SportingActivity[]
     */
    
    /**
 * Retourne les activités sportives d'un patient
 * pour une journée donnée, triées chronologiquement.
 *
 * @return SportingActivity[]
 */
    public function findForPatientAndDate(User $patient, \DateTimeInterface $date): array
    {
    $start = \DateTimeImmutable::createFromInterface($date)->setTime(0, 0, 0);
    $end = $start->modify('+1 day');

    return $this->createQueryBuilder('s')
        ->andWhere('s.patient = :patient')
        ->andWhere('s.date >= :start')
        ->andWhere('s.date < :end')
        ->setParameter('patient', $patient)
        ->setParameter('start', $start)
        ->setParameter('end', $end)
        ->orderBy('s.hour', 'ASC')
        ->getQuery()
        ->getResult();
     }
    
     public function findRecentesPourPatient(User $patient, int $limite = 30): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.patient = :patient')
            ->setParameter('patient', $patient)
            ->orderBy('s.date', 'DESC')
            ->addOrderBy('s.hour', 'DESC')
            ->setMaxResults($limite)
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return SportingActivity[] Returns an array of SportingActivity objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SportingActivity
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}