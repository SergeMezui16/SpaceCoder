<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\Project;
use App\Entity\Ressource;
use Doctrine\ORM\EntityManagerInterface;

class EntityService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {}

    public function __destruct()
    {
        $this->entityManager->flush();
    }

    public function incrementProjectVisits(Project $project)
    {
        $project->setVisit($project->getVisit() + 1);
    }

    public function incrementArticleViews(Article $article)
    {
        $article->setViews($article->getViews() + 1);
    }

    public function incrementRessourceClicks(Ressource $ressource)
    {
        $ressource->setClicks($ressource->getClicks() + 1);
    }
}