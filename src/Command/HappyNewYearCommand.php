<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\EarnCoinService;
use App\Service\MailMakerService;
use App\Service\ScheduleService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(
    name: 'app:happy-new-year',
    description: 'Send an email to all users to wish Happy New Year',
)]
class HappyNewYearCommand extends Command
{

    public function __construct(
        private UserRepository $users,
        private MailMakerService $mailer,
        private EarnCoinService $earner,
        private ScheduleService $schedule
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if($this->schedule->isNewYear()){
            $io->info('Not New Year Day.');
            return Command::SUCCESS;
        }

        $stopwatch = new Stopwatch();
        $stopwatch->start('happy-new-year-command');
        
        $allUsers = $this->users->findAll();


        // Send Mails
        foreach ($allUsers as $user) {
            $this->earner->happyNewYear($user);
            $this->mailer->make($user->getAuth()->getEmail(), 'Bonne Année', 'mail/command/happy_new_year.html.twig', [
                'pseudo' => $user->getPseudo(),
                'subject' => 'Bonne Année',
                'rho' => EarnCoinService::NEW_YEAR
            ])->send();
        }

        $createUserArray = static function (User $user) {
            return [
                $user->getId(),
                $user->getPseudo(),
                $user->getAuth()->getEmail(),
                implode(', ', $user->getAuth()->getRoles()),
            ];
        };

        $usersAsPlainArrays = array_map($createUserArray, $allUsers);

        $bufferedOutput = new BufferedOutput();
        $io = new SymfonyStyle($input, $bufferedOutput);

        $io->table(
            ['ID', 'Pseudo', 'Email', 'Roles'],
            $usersAsPlainArrays
        );

        $table = $bufferedOutput->fetch();

        $event = $stopwatch->stop('happy-new-year-command');
        $message = $table . sprintf('\nTotal Users: %d / Elapsed time: %.2f ms / Consumed memory: %.2f MB', count($allUsers), $event->getDuration(), $event->getMemory() / (1024 ** 2));
        
        // Send Report
        $this->mailer->make('contact@spacecoder.fun', 'Bonne Année', 'mail/command/report.html.twig', [
            'result' => $message,
            'subject' => 'Bonne Année',
            'rho' => EarnCoinService::NEW_YEAR
        ])->send();

        $io = new SymfonyStyle($input, $output);
        $io->success('All is ok.');

        return Command::SUCCESS;
    }
}
