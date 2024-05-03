<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\MailMakerService;
use App\Service\SearchService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('pages/home/index.html.twig', [

        ]);
    }

    #[Route('/search', name: 'search')]
    public function search(Request $request, SearchService $searchService): Response
    {
        return $this->render('pages/home/search.html.twig', [
            'title' => 'Recherche',
            'search' => $searchService->search($request->query->get('q', ''), $request->query->getInt('page', 1), 50, 10)
        ]);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(Request$request, MailMakerService $mailer, EntityManagerInterface $em): Response
    {
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $contact
                ->setUser($this->getUser())
                ->setDone(false)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable())
            ;
            
            $em->persist($contact);
            $em->flush();

            $mailer->make('contact@spacecoder.fun', 'Contact - SpaceCoder', 'mail/home/contact.html.twig', [
                'contact' => $contact,
                'Subject' => 'Contact',
            ])->send();

            $this->addFlash('success', 'Votre message a été envoyé avec success.');

            return $this->redirectToRoute('contact');
        }

        return $this->render('pages/home/contact.html.twig', [
            'title' => 'Contact',
            'form' => $form
        ]);
    }


    #[Route('/terms-and-conditions', name: 'terms')]
    public function terms(): Response
    {
        return $this->render('pages/home/terms_and_conditions.html.twig', [
            'title' => 'Conditions d\'utilisation'
        ]);
    }
}
