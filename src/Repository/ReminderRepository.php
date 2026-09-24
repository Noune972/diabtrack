<?php

namespace App\Repository;

use App\Entity\Reminder;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reminder>
 */
class ReminderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reminder::class);
    }

    /**
     * Retourne tous les rappels d'un patient,
     * classés par heure.
     *
     * @return Reminder[]
     */
    public function findForPatient(User $patient): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.patient = :patient')
            ->setParameter('patient', $patient)
            ->orderBy('r.time', 'ASC')
            ->getQuery()
            ->getResult();
    }
}