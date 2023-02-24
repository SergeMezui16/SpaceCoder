<?php
namespace App\Service;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

/**
 * Earn Coin Manager
 * 
 * This Service manage how Coins should be share between users 
 * organized by kind of actions
 */
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
        private EntityManagerInterface $manager,
        private NotificationService $notifier
    )
    {
        $this->flash = (new Session(new PhpBridgeSessionStorage()))->getFlashBag();
    }

    public function __destruct()
    {
        $this->manager->flush();
    }

    /**
     * Earn Coins if User is the first viewer of this Article
     *
     * @param Article $article
     * @param User $user
     * @return User
     */
    public function firstViewer(Article $article, User $user): User
    {
        if($this->isFirstViewer($article)) {
            $user->setCoins($user->getCoins() + EarnCoinService::FIRST_VIEWER);
            $this->flash->add('success', EarnCoinService::FIRST_VIEWER_PHRASE);
            $this->notifier->notifyOnFirstViewOnArticle($user);
        }
        return $user;
    }

    /**
     * Earn Coins if User is the first to comment this Article
     *
     * @param Article $article
     * @param User $user
     * @return User
     */
    public function firstCommentOn(Article $article, User $user): User
    {
        if ($this->isFirstCommentOn($article)) {
            $user->setCoins($user->getCoins() + EarnCoinService::FIRST_COMMENT_ON);
            $this->flash->add('success', EarnCoinService::FIRST_COMMENT_ON_PHRASE);
            $this->notifier->notifyOnFirstCommentOnArticle($user);
        }
        return $user;
    }

    /**
     * Earn Coins if User icomment for the first time
     *
     * @param Article $article
     * @return User
     */
    public function firstComment(User $user): User
    {
        if ($this->isFirstComment($user)) {
            $user->setCoins($user->getCoins() + EarnCoinService::FIRST_COMMENT);
            $this->flash->add('success', EarnCoinService::FIRST_COMMENT_PHRASE);
            $this->notifier->notifyOnfirstComment($user);
        }
        return $user;
    }

    /**
     * Earn Coins if User make a coment
     *
     * @param User $user
     * @return User
     */
    public function commentOn(User $user): User
    {
        $this->flash->add('success', EarnCoinService::COMMENT_ON_PHRASE);
        return $user->setCoins($user->getCoins() + EarnCoinService::COMMENT_ON);
    }

    /**
     * Earn Coins if it Happy New Year day
     *
     * @param User $user
     * @return User
     */
    public function happyNewYear(User $user): User
    {
        return $user->setCoins($user->getCoins() + EarnCoinService::NEW_YEAR);
    }





    /**
     * Check if Article has any comment yet
     *
     * @param Article $article
     * @return boolean
     */
    public function isFirstCommentOn(Article $article): bool
    {
        return count($article->getComments()) === 0;
    }

    /**
     * Check if User has any comment yet
     *
     * @param User $user
     * @return boolean
     */
    public function isFirstComment(User $user): bool
    {
        return count($user->getComments()) === 0;
    }

    /**
     * Check if Article has any view yet
     *
     * @param Article $article
     * @return boolean
     */
    public function isFirstViewer(Article $article): bool
    {
        return $article->getViews() === 0;
    }
    
}