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
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();

        if (!$request->isMethod('GET') || !$response->isSuccessful()) {
            return;
        }

        $path = $request->getPathInfo();

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

        // Une même session de navigateur n'est comptée
        // qu'une seule fois par jour.
        $session = $request->getSession();
        $today = (new \DateTimeImmutable())->format('Y-m-d');

        if ($session->get('platform_visit_day') === $today) {
            return;
        }

        $visit = new PlatformVisit();
        $visit->setVisitedAt(new \DateTimeImmutable());
        $visit->setPath($path);

        $this->entityManager->persist($visit);
        $this->entityManager->flush();

        $session->set('platform_visit_day', $today);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent::class => 'onResponseEvent',
        ];
    }
}