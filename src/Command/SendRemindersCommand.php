<?php

namespace App\Command;

use App\Entity\Notification;
use App\Entity\Reminder;
use App\Repository\ReminderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'app:send-reminders',
    description: 'Envoie les rappels DiabTrack arrivés à échéance.',
)]
class SendRemindersCommand extends Command
{
    public function __construct(
        private readonly ReminderRepository $reminderRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly MailerInterface $mailer,
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $io = new SymfonyStyle($input, $output);

        $timezone = new \DateTimeZone('Europe/Paris');
        $now = new \DateTimeImmutable('now', $timezone);

        $reminders = $this->reminderRepository->findAll();

        $processedCount = 0;

        foreach ($reminders as $reminder) {
            if (!$this->isDue($reminder, $now)) {
                continue;
            }

            $patient = $reminder->getPatient();
            $type = $reminder->getType();

            if (!$patient || !$type) {
                continue;
            }

            $deliveryMethod = $reminder->getDeliveryMethod();

            try {
                /*
                 * E-MAIL
                 */
                if (
                    $deliveryMethod === 'email'
                    || $deliveryMethod === 'both'
                ) {
                    if (!$patient->getEmail()) {
                        $io->warning(sprintf(
                            'Le rappel #%d nécessite un e-mail, mais le patient n\'a pas d\'adresse e-mail.',
                            $reminder->getId()
                        ));

                        continue;
                    }

                    $email = (new Email())
                        ->from('no-reply@diabtrack.fr')
                        ->to($patient->getEmail())
                        ->subject(
                            'Rappel DiabTrack — '.$type->label()
                        )
                        ->html($this->buildEmail($reminder));

                    $this->mailer->send($email);

                    $io->writeln(sprintf(
                        'E-mail envoyé à %s : %s',
                        $patient->getEmail(),
                        $type->label()
                    ));
                }

                /*
                 * NOTIFICATION DIABTRACK
                 */
                if (
                    $deliveryMethod === 'notification'
                    || $deliveryMethod === 'both'
                ) {
                    $notification = new Notification();

                    $notification
                        ->setPatient($patient)
                        ->setMessage(
                            $type->icon()
                            .' '
                            .$type->label()
                            .' — rappel prévu à '
                            .$reminder->getTime()?->format('H:i')
                        )
                        ->setIsRead(false)
                        ->setCreatedAt($now);

                    $this->entityManager->persist($notification);

                    $io->writeln(sprintf(
                        'Notification DiabTrack créée : %s',
                        $type->label()
                    ));
                }

                /*
                 * On marque le rappel comme traité seulement
                 * après la création/envoi demandé.
                 */
                $reminder->setLastSentAt($now);

                ++$processedCount;
            } catch (\Throwable $exception) {
                $io->error(sprintf(
                    'Impossible de traiter le rappel #%d : %s',
                    $reminder->getId(),
                    $exception->getMessage()
                ));
            }
        }

        $this->entityManager->flush();

        $io->success(sprintf(
            '%d rappel(s) traité(s).',
            $processedCount
        ));

        return Command::SUCCESS;
    }

    private function isDue(
        Reminder $reminder,
        \DateTimeImmutable $now
    ): bool {
        $startDate = $reminder->getStartDate();
        $time = $reminder->getTime();

        if (!$startDate || !$time) {
            return false;
        }

        $timezone = new \DateTimeZone('Europe/Paris');

        $start = new \DateTimeImmutable(
            $startDate->format('Y-m-d').' 00:00:00',
            $timezone
        );

        $today = $now->setTime(0, 0);

        if ($today < $start) {
            return false;
        }

        $scheduledToday = $today->setTime(
            (int) $time->format('H'),
            (int) $time->format('i')
        );

        if ($now < $scheduledToday) {
            return false;
        }

        /*
         * Empêche plusieurs traitements le même jour.
         */
        $lastSentAt = $reminder->getLastSentAt();

        if (
            $lastSentAt !== null
            && $lastSentAt->format('Y-m-d') === $now->format('Y-m-d')
        ) {
            return false;
        }

        return match ($reminder->getFrequency()) {
            'daily' => true,

            'weekdays' => $this->isWeekday($today),

            'weekly' => $this->isWeeklyOccurrence(
                $start,
                $today,
                1
            ),

            'biweekly' => $this->isWeeklyOccurrence(
                $start,
                $today,
                2
            ),

            'monthly' => $this->isMonthlyOccurrence(
                $start,
                $today,
                1
            ),

            'quarterly' => $this->isMonthlyOccurrence(
                $start,
                $today,
                3
            ),

            'semiannual' => $this->isMonthlyOccurrence(
                $start,
                $today,
                6
            ),

            'yearly' => $this->isYearlyOccurrence(
                $start,
                $today
            ),

            'once' => $today->format('Y-m-d')
                === $start->format('Y-m-d'),

            default => false,
        };
    }

    private function isWeekday(
        \DateTimeImmutable $date
    ): bool {
        $day = (int) $date->format('N');

        return $day >= 1 && $day <= 5;
    }

    private function isWeeklyOccurrence(
        \DateTimeImmutable $start,
        \DateTimeImmutable $today,
        int $intervalWeeks
    ): bool {
        if ($today < $start) {
            return false;
        }

        $days = (int) $start->diff($today)->format('%a');

        return $days % (7 * $intervalWeeks) === 0;
    }

    private function isMonthlyOccurrence(
        \DateTimeImmutable $start,
        \DateTimeImmutable $today,
        int $intervalMonths
    ): bool {
        if ($start->format('d') !== $today->format('d')) {
            return false;
        }

        $months = (
            ((int) $today->format('Y')
                - (int) $start->format('Y')) * 12
        ) + (
            (int) $today->format('n')
                - (int) $start->format('n')
        );

        return $months >= 0
            && $months % $intervalMonths === 0;
    }

    private function isYearlyOccurrence(
        \DateTimeImmutable $start,
        \DateTimeImmutable $today
    ): bool {
        return $start->format('m-d')
            === $today->format('m-d');
    }

    private function buildEmail(Reminder $reminder): string
    {
        $type = $reminder->getType();

        $icon = $type?->icon() ?? '🔔';
        $label = $type?->label() ?? 'Rappel';

        $safeIcon = htmlspecialchars(
            $icon,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        $safeLabel = htmlspecialchars(
            $label,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        $time = $reminder->getTime()?->format('H:i') ?? '';

        return <<<HTML
        <div style="
            max-width:600px;
            margin:0 auto;
            font-family:Arial,sans-serif;
            color:#243B53;
        ">
            <div style="
                background:#102A43;
                padding:24px;
                border-radius:16px 16px 0 0;
                color:white;
            ">
                <div style="
                    font-size:22px;
                    font-weight:bold;
                ">
                    DiabTrack
                </div>

                <div style="
                    margin-top:5px;
                    color:#B8EDE7;
                    font-size:14px;
                ">
                    Votre rappel santé
                </div>
            </div>

            <div style="
                padding:28px;
                border:1px solid #E6EEF5;
                border-top:0;
                border-radius:0 0 16px 16px;
            ">
                <div style="
                    font-size:34px;
                    margin-bottom:14px;
                ">
                    {$safeIcon}
                </div>

                <h2 style="
                    margin:0;
                    color:#102A43;
                    font-size:21px;
                ">
                    {$safeLabel}
                </h2>

                <p style="
                    margin-top:16px;
                    font-size:15px;
                    line-height:1.6;
                ">
                    C'est l'heure de votre rappel DiabTrack.
                </p>

                <div style="
                    margin-top:20px;
                    padding:15px;
                    background:#F0FDFA;
                    border-radius:10px;
                    color:#115E59;
                    font-weight:bold;
                ">
                    Heure prévue : {$time}
                </div>

                <p style="
                    margin-top:24px;
                    color:#627D98;
                    font-size:12px;
                    line-height:1.5;
                ">
                    Ce message est un rappel configuré dans votre compte
                    DiabTrack.
                </p>
            </div>
        </div>
        HTML;
    }
}