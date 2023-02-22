<?php

namespace App\Authentication\Controller;

use App\Authentication\Entity\UserAuthentication;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/notifications')]
#[IsGranted('IS_AUTHENTICATED')]
class NotificationController extends AbstractController
{
    #[Route('', name: 'notification')]
    public function index(): Response
    {
        /** @var UserAuthentication $auth */
        $auth = $this->getUser();
        $notifications = $auth->getUser()->getLastsNotifications();

        return $this->render('authentication/notification/index.html.twig', [
            'notifications' => $notifications,
            'to_see' => $auth->getUser()->countUnseeNotification()
        ]);
    }

    #[Route('/see/{id}', name: 'notification_see')]
    public function see(Notification $notification, EntityManagerInterface $manager): JsonResponse
    {
        if(!$notification->isSaw()){
            $notification->setSaw(true);
    
            $manager->persist($notification);
            $manager->flush();
        }

        return $this->json(
            $notification->getAction()
        );
    }
}
 