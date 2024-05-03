<?php

namespace App\EventSubscriber;

use App\Entity\Comment;
use App\Entity\User;
use App\Event\CommentCreatedEvent;
use App\Service\EarnCoinService;
use App\Service\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CommentCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EarnCoinService $earner,
        private readonly NotificationService $notifier
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            CommentCreatedEvent::class => 'onCommentCreatedEvent',
        ];
    }


    /**
     * Earn Coins to the User and Notify him 
     * if it's a reply to another one
     *
     * @param CommentCreatedEvent $event
     * @return void
     */
    public function onCommentCreatedEvent(CommentCreatedEvent $event): void
    {
        $comment = $event->getComment();
        $auth = $event->getAuth();

        $this->earnCoins($auth->getUser(), $comment);
        $this->notifier->notifyArticleAuthorOnComment($comment);
        $this->notifier->notifyReplyToAuthorOnComment($comment);
    }

    /**
     * Earn coins to comment author
     *
     * @param User $user
     * @param Comment $comment
     * @return void
     */
    public function earnCoins(User $user, Comment $comment): void
    {
        $this->earner->commentOn($user);
        $this->earner->firstComment($user);
        $this->earner->firstCommentOn($comment->getArticle(), $user);
    }
}
