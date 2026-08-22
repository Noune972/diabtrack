<?php

namespace App\Repository;

use App\Entity\Insuline;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Insuline>
 */
class InsulineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Insuline::class);
    }

    /**
     * Retourne les injections d'insuline d'un patient donné,
     * triées de la plus récente à la plus ancienne (date puis heure).
     *
     * @return Insuline[]
     */
    public function findRecentesPourPatient(User $patient, int $limite = 30): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.patient = :patient')
            ->setParameter('patient', $patient)
            ->orderBy('i.date', 'DESC')
            ->addOrderBy('i.hour', 'DESC')
            ->setMaxResults($limite)
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Insuline[] Returns an array of Insuline objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Insuline
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}