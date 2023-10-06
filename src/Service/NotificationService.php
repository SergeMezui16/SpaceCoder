<?php
namespace App\Service;

use App\Entity\Comment;
use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use App\Service\EarnCoinService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Notification Service
 * 
 * Permit you to create a notification and persist its 
 * in the same way.
 */
class NotificationService
{

    public function __construct(
        private NotificationRepository $notificationRepository,
        private EntityManagerInterface $manager,
        private UrlGeneratorInterface $url,
    )
    {}

    /**
     * Create and persist a notification 
     *
     * @param User[] $recipients Users that will receive the notification
     * @param string $content The content of the notification. That could be a text or HTML
     * @param string $title The title of the notification
     * @param string $action The route where the notification bring
     * @param string $header choose between comment, new, gift or account
     * @return Notification
     */
    public function notify(
        array $recipients, 
        string $title, 
        string $content, 
        string $action, 
        string $header
    ): Notification
    {
        $notification = (new Notification())
            ->setHeader($header)
            ->setContent($content)
            ->setTitle($title)
            ->setAction($action)
            ->setSentAt(new \DateTimeImmutable())
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable())
        ;

        foreach ($recipients as $recipient) $notification->addRecipient($recipient);

        $this->manager->persist($notification);
        $this->manager->flush();

        return $notification;
    }


    /**
     * Notify the owner of the artcile
     * when a comment has submited
     *
     * @param Comment $comment Comment submited
     * @return Notification|null return null if the User don't exist
     */
    public function notifyArticleAuthorOnComment(Comment $comment): ?Notification
    {
        $article = $comment->getArticle();

        if ($article->getAuthor() instanceof User) {
            return $this->notify(
                [$article->getAuthor()],
                'Un nouveau commentaire',
                sprintf(
                    '%s vient de poster un commentaire sur votre article <strong>%s</strong>, cliquez pour voir ce nouveau commentaire.',
                    $comment->getAuthor(),
                    $article
                ),
                $this->url->generate('article_detail', [
                    'slug' => $article->getSlug(),
                    '_fragment' => 'comment-' . $comment->getId()
                ]),
                'comment'
            );
        }

        return null;
    }


    /**
     * Notify the comment's owner if is a reply
     * when comment has submited
     *
     * @param Comment $comment
     * @return Notification|null
     */
    public function notifyReplyToAuthorOnComment(Comment $comment): ?Notification
    {
        $replyTo = $comment->getReplyTo();

        if ($replyTo instanceof Comment && $replyTo->getAuthor() !== null) {
            return $this->notify(
                [$replyTo->getAuthor()],
                'Reponse à votre commentaire',
                sprintf(
                    '%s vient de répondre à votre commentaire à l\'article <strong>%s</strong>, cliquez pour voir ce nouveau commentaire.',
                    $comment->getAuthor(),
                    $comment->getArticle()
                ),
                $this->url->generate('article_detail', [
                    'slug' => $replyTo->getArticle()->getSlug(),
                    '_fragment' => 'comment-' . $comment->getId()
                ]),
                'comment'
            );
        }

        return null;
    }

    /**
     * Notify a Welcome to new User
     * after email verification on inscription
     *
     * @param User $user
     * @return Notification
     */
    public function notifyOnWelcome(User $user): Notification
    {
        return $this->notify(
            [$user],
            'Welcome',
            'Merci d\'avoir rejoint l\'équipage de <strong>SpaceCoder</strong>. Pour vous accueillir nous vous avons offert <span class="rho">10</span> (Rhô).',
            $this->url->generate('profile', ['slug' => $user->getSlug()]),
            'gift'
        );
    }

    /**
     * Notify user when password has been changed
     *
     * @param User $user
     * @return void
     */
    public function notifyOnPasswordChanged(User $user)
    {
        return $this->notify(
            [$user],
            'Mot de passe changé avec succès',
            'Vous venez de changer votre mot de passe. Votre mot de passe a été modifié avec succès. Vous pouvez désormais l’utiliser.',
            $this->url->generate('profile', ['slug' => $user->getSlug()]),
            'account'
        );
    }

    /**
     * Notify user when password has succesfully reseted
     *
     * @param User $user
     * @return void
     */
    public function notifyOnResetPassword(User $user)
    {
        return $this->notify(
            [$user],
            'Mot de passe changé avec succès',
            'Vous venez de faire une demande de restauration de mot de passe. Votre mot de passe a été changé avec succès. Vous pouvez désormais l’utiliser.',
            $this->url->generate('profile', ['slug' => $user->getSlug()]),
            'account'
        );
    }

    /**
     * Notify user when he is the first viewer of an article
     *
     * @param User $user
     * @return void
     */
    public function notifyOnFirstViewOnArticle(User $user)
    {
        return $this->notify(
            [$user],
            'Premier de la course',
            EarnCoinService::FIRST_VIEWER_PHRASE,
            $this->url->generate('profile', ['slug' => $user->getSlug()]),
            'gift'
        );
    }

    /**
     * Notify user if he is the first to comment on an article
     *
     * @param User $user
     * @return void
     */
    public function notifyOnFirstCommentOnArticle(User $user)
    {
        return $this->notify(
            [$user],
            'Premier de la course',
            EarnCoinService::FIRST_COMMENT_ON_PHRASE,
            $this->url->generate('profile', ['slug' => $user->getSlug()]),
            'gift'
        );
    }

    /**
     * Notify user if it is his first comment
     *
     * @param User $user
     * @return void
     */
    public function notifyOnfirstComment(User $user)
    {
        return $this->notify(
            [$user],
            'Premier commentaire',
            EarnCoinService::FIRST_COMMENT_PHRASE,
            $this->url->generate('profile', ['slug' => $user->getSlug()]),
            'gift'
        );
    }

    
}