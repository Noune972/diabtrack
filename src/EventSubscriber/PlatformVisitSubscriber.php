<?php

namespace App\EventSubscriber;

use App\Entity\PlatformVisit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class PlatformVisitSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function onResponseEvent(ResponseEvent $event): void
    {
        // Ne compter que la requête principale.
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();

        // Ne compter que les consultations GET réussies.
        if (!$request->isMethod('GET') || !$response->isSuccessful()) {
            return;
        }

        $path = $request->getPathInfo();

        // Exclure les pages techniques et l'administration.
        $excludedPrefixes = [
            '/admin',
            '/assets',
            '/build',
            '/_profiler',
            '/_wdt',
            '/2fa',
        ];

        foreach ($excludedPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return;
            }
        }

        $visit = new PlatformVisit();
        $visit->setVisitedAt(new \DateTimeImmutable());
        $visit->setPath($path);

        $this->entityManager->persist($visit);
        $this->entityManager->flush();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent::class => 'onResponseEvent',
        ];
    }
}
