<?php

namespace App\Command;

use App\Authentication\Entity\UserAuthentication;
use App\Authentication\Repository\UserAuthenticationRepository;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(
    name: 'app:create-user',
    description: 'Create a new User.',
    aliases: ['app:cu']
)]
class CreateUserCommand extends Command
{
    private SymfonyStyle $io;
    private string $pseudo;
    private string $email;
    private string $password;
    private string $country;
    private int $coins;
    private bool $isHashed;


    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $users,
        private UserAuthenticationRepository $auths
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getHelp());
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $this->io->title('Add User Command Interactive Wizard');
        $this->io->text([
            'Now we\'ll ask you for the value of your user.',
        ]);

        $this->pseudo = $this->io->ask('Pseudo');

        if ($this->users->findOneBy(['pseudo' => $this->pseudo]) !== null) {
            throw new RuntimeException(sprintf('There is already a user registered with the "%s" pseudo.', $this->pseudo));
        }

        $this->email = $this->io->ask('Email');

        if ($this->auths->findOneBy(['email' => $this->email]) !== null) {
            throw new RuntimeException(sprintf('There is already a user registered with the "%s" email.', $this->email));
        }

        $this->password = $this->io->ask('Password');
        $this->isHashed = $this->io->confirm('Did we hash the password ?', false);
        $this->country = $this->io->ask('Country', '');
        $this->coins = (int) $this->io->ask('coins', 10);
    }



    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('create-user-command');

        $auth = new UserAuthentication();

        if ($this->isHashed) {
            $this->password = $this->passwordHasher->hashPassword($auth, $this->password);
        }
        
        $user = (new User())
            ->setPseudo($this->pseudo)
            ->setAuth(
                $auth
                    ->setEmail($this->email)
                    ->setPassword($this->password)
                    ->setBlocked(false)
            )
            ->setCountry($this->country)
            ->setBornAt(new \DateTimeImmutable())
            ->setCoins($this->coins)
        ;

        $this->em->persist($auth);
        $this->em->persist($user);
        $this->em->flush();

        $this->io->success(sprintf('User was successfully created: %s (%s)',
            $user->getPseudo(),
            $auth->getEmail()
        ));

        $event = $stopwatch->stop('create-user-command');

        if ($output->isVerbose()) {
            $this->io->comment(sprintf('New user database id: %d / Elapsed time: %.2f ms / Consumed memory: %.2f MB', $auth->getId(), $event->getDuration(), $event->getMemory() / (1024 ** 2)));
        }

        return Command::SUCCESS;
    }



    private function getCommandHelp(): string
    {
        return <<<'HELP'
            The <info>%command.name%</info> command creates new users and saves them in the database:
              <info>php %command.full_name%</info>
            HELP;
    }
}
