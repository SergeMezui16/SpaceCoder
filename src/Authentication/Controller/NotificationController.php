<?php

namespace App\Authentication\Controller;

use App\Authentication\Entity\UserAuthentication;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    #[Route('/notification', name: 'notification')]
    public function index(EntityManagerInterface $manager, NotificationRepository $notificationRepository): Response
    {
        /** @var UserAuthentication $auth */
        $auth = $this->getUser();

        $notifications = $notificationRepository->findAllByRecipent($auth->getUser());
        $last = [];
        $toSee = 0;


        foreach($notifications as $notif){
            $last[] = clone $notif;
            if($notif->isSaw() === false){
                $toSee++;

                $notif->setSaw(true);
                $manager->persist($notif);
            }
            
        }
        
        $manager->flush();

        return $this->render('authentication/notification/index.html.twig', [
            'notifications' => $last,
            'to_see' => $toSee
        ]);
    }
}
 