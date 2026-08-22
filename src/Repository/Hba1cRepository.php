<?php

namespace App\Repository;

use App\Entity\Hba1c;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Hba1c>
 */
class Hba1cRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hba1c::class);
    }

    /**
     * Retourne les mesures d'HbA1c d'un patient donné,
     * triées de la plus récente à la plus ancienne (date puis heure).
     *
     * @return Hba1c[]
     */
    public function findRecentesPourPatient(User $patient, int $limite = 30): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.patient = :patient')
            ->setParameter('patient', $patient)
            ->orderBy('h.date', 'DESC')
            ->addOrderBy('h.hour', 'DESC')
            ->setMaxResults($limite)
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Hba1c[] Returns an array of Hba1c objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Hba1c
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}