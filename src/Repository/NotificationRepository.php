<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * Retourne les dernières notifications d'un patient.
     *
     * @return Notification[]
     */
    public function findLatestForPatient(
        User $patient,
        int $limit = 10
    ): array {
        return $this->createQueryBuilder('n')
            ->andWhere('n.patient = :patient')
            ->setParameter('patient', $patient)
            ->orderBy('n.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte les notifications non lues d'un patient.
     */
    public function countUnreadForPatient(User $patient): int
    {
        return (int) $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->andWhere('n.patient = :patient')
            ->andWhere('n.isRead = :isRead')
            ->setParameter('patient', $patient)
            ->setParameter('isRead', false)
            ->getQuery()
            ->getSingleScalarResult();
    }
}