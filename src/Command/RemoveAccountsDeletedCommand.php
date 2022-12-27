<?php

namespace App\Command;

use App\Authentication\Repository\UserAuthenticationRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(
    name: 'app:remove-accounts-deleted',
    description: 'Delete all user accounts if they have a delete demand.',
    aliases: ['app:rad']
)]
class RemoveAccountsDeletedCommand extends Command
{
    public function __construct(
        private UserAuthenticationRepository $auths
    ) {
        parent::__construct();
    }
    
    protected function configure(): void
    {
        $this
            // ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            // ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('delete-users-command');
        
        $accounts = $this->auths->findAllDeleted();
        $count = count($accounts);

        foreach ($accounts as $auth) {

            foreach ($auth->getUser()->getComments() as $comment) {
                $comment->setAuthor(null);
            }

            foreach ($auth->getUser()->getSuggestions() as $article) {
                $article->setSuggestedBy(null);
            }

            foreach ($auth->getUser()->getArticles() as $article) {
                $article->setAuthor(null);
            }

            $this->em->flush();

            $this->em->remove($auth->getUser());
            $this->em->remove($auth);

            $this->em->flush();
        }

        if($count > 0){
            $this->io->success(sprintf(
                '%i User was successfully deleted: %s (%s).',
                count($accounts),
                $auth->getUser()->getPseudo(),
                $auth->getEmail()
            ));
        } else{
            $this->io->info(sprintf(
                'No User found.'
            ));
        }

        $event = $stopwatch->stop('delete-users-command');

        $this->io->comment(sprintf('New user database id: %d / Elapsed time: %.2f ms / Consumed memory: %.2f MB', 10, $event->getDuration(), $event->getMemory() / (1024 ** 2)));

        return Command::SUCCESS;
    }
}
