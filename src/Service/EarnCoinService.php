<?php
namespace App\Service;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class EarnCoinService
{

    public const FIRST_VIEWER = 5;
    public const FIRST_COMMENT_ON = 10;
    public const FIRST_COMMENT = 10;
    public const COMMENT_ON = 2;
    public const NEW_YEAR = 20;


    public const FIRST_VIEWER_PHRASE = 'Felicitation !!! Vous avez gagné <span class=\'rho\'>'.EarnCoinService::FIRST_VIEWER.'</span> pour avoir été le premier utilisateur à voir ce tutoriel. Continuez comme ça.';
    public const FIRST_COMMENT_ON_PHRASE = 'Felicitation !!! Vous avez gagné <span class=\'rho\'>'.EarnCoinService::FIRST_COMMENT_ON.'</span> pour avoir été le premier utilisateur à commenter ce tutoriel. Continuez comme ça.';
    public const FIRST_COMMENT_PHRASE = 'Felicitation !!! Vous avez gagné <span class=\'rho\'>'.EarnCoinService::FIRST_COMMENT.'</span> pour avoir écrit votre premier commentaire. Continuez comme ça.';
    public const COMMENT_ON_PHRASE = 'Felicitation !!! Vous avez gagné <span class=\'rho\'>'.EarnCoinService::COMMENT_ON.'</span> pour avoir écrit ce commentaire. Continuez comme ça.';

    private FlashBagInterface $flash;

    public function __construct(
        private EntityManagerInterface $manager
    )
    {
        $this->flash = (new Session())->getFlashBag();
    }

    public function __destruct()
    {
        $this->manager->flush();
    }


    public function firstViewer(Article $article, User $user): User
    {
        if($this->isFirstViewer($article)) {
            $user->setCoins($user->getCoins() + EarnCoinService::FIRST_VIEWER);
            $this->flash->add('success', EarnCoinService::FIRST_VIEWER_PHRASE);
        }
        return $user;
    }

    public function firstCommentOn(Article $article, User $user): User
    {
        if ($this->isFirstCommentOn($article)) {
            $user->setCoins($user->getCoins() + EarnCoinService::FIRST_COMMENT_ON);
            $this->flash->add('success', EarnCoinService::FIRST_COMMENT_ON_PHRASE);
        }
        return $user;
    }

    public function firstComment(User $user): User
    {
        if ($this->isFirstComment($user)) {
            $user->setCoins($user->getCoins() + EarnCoinService::FIRST_COMMENT);
            $this->flash->add('success', EarnCoinService::FIRST_COMMENT_PHRASE);
        }
        return $user;
    }

    public function commentOn(User $user): User
    {
        $this->flash->add('success', EarnCoinService::COMMENT_ON_PHRASE);
        return $user->setCoins($user->getCoins() + EarnCoinService::COMMENT_ON);
    }

    public function happyNewYear(User $user): User
    {
        return $user->setCoins($user->getCoins() + EarnCoinService::NEW_YEAR);
    }



    

    public function isFirstCommentOn(Article $article): bool
    {
        return count($article->getComments()) === 0;
    }

    public function isFirstComment(User $user): bool
    {
        return count($user->getComments()) === 0;
    }


    public function isFirstViewer(Article $article): bool
    {
        return $article->getViews() === 0;
    }
    
}