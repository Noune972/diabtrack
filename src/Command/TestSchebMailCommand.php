<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Email\Generator\CodeGeneratorInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:test-scheb-mail',
    description: 'Test complet du générateur email Scheb',
)]
class TestSchebMailCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,

        #[Autowire(service: 'scheb_two_factor.security.email.default_code_generator')]
        private readonly CodeGeneratorInterface $generator,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = $this->em->getRepository(User::class)->find(1);

        if (!$user) {
            $output->writeln('Utilisateur introuvable.');
            return Command::FAILURE;
        }

        $output->writeln('Destinataire : '.$user->getEmailAuthRecipient());

        $this->generator->generateAndSend($user);

        $output->writeln('Code généré : '.$user->getEmailAuthCode());
        $output->writeln('generateAndSend() terminé.');

        return Command::SUCCESS;
    }
}