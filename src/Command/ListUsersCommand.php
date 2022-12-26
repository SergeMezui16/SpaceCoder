<?php

namespace App\Command;

use App\Authentication\Entity\UserAuthentication;
use App\Authentication\Repository\UserAuthenticationRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:list-user',
    description: 'Lists all the existing users',
    aliases: ['app:users']
)]
class ListUsersCommand extends Command
{
    public function __construct(
        private UserAuthenticationRepository $auths
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setHelp($this->getHelp())
            ->addOption('max-results', '-m', InputOption::VALUE_OPTIONAL, 'Limits the number of users listed', 50)
            ->addOption('only-blocked', '-b', InputOption::VALUE_OPTIONAL, 'Only users blocked', false)
        ;
    }


    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $input->getOption('only-blocked') === null ? $input->setOption('only-blocked', true) : $input->setOption('only-blocked', false);
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var int|null $maxResults */
        $maxResults = $input->getOption('max-results');
        /** @var bool|null $maxResults */
        $onlyBlocked = $input->getOption('only-blocked');

        $params = [];
        
        if($onlyBlocked) $params['blocked'] = true;

        $allUsers = $this->auths->findBy($params, ['id' => 'DESC'], $maxResults);

        $createUserArray = static function (UserAuthentication $auth) {
            return [
                $auth->getUser()->getId(),
                $auth->getUser()->getPseudo(),
                $auth->getEmail(),
                implode(', ', $auth->getRoles()),
            ];
        };

        $usersAsPlainArrays = array_map($createUserArray, $allUsers);
        
        $bufferedOutput = new BufferedOutput();
        $io = new SymfonyStyle($input, $bufferedOutput);
        $io->table(
            ['ID', 'Pseudo', 'Email', 'Roles'],
            $usersAsPlainArrays
        );

        $usersAsATable = $bufferedOutput->fetch();
        $output->write($usersAsATable);

        return Command::SUCCESS;
    }

    private function getCommandHelp(): string
    {
        return <<<'HELP'
                The <info>%command.name%</info> command lists all the users registered in the application:
                  <info>php %command.full_name%</info>
                By default the command only displays the 50 most recent users. Set the number of
                results to display with the <comment>--max-results</comment> option:
                  <info>php %command.full_name%</info> <comment>--max-results=2000</comment>
                HELP;
    }
}
