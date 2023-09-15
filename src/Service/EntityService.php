<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\Project;
use App\Entity\Ressource;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Entity Service
 * 
 * This Service provide many methods for Entities
 */
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

    /**
     * Increments visits of Project
     *
     * @param Project $project
     * @return Project
     */
    public function incrementProjectVisits(Project $project): Project
    {
        return $project->setVisit($project->getVisit() + 1);
    }

    /**
     * Increments views of Article
     *
     * @param Article $article
     * @return Article
     */
    public function incrementArticleViews(Article $article): Article
    {
        return $article->setViews($article->getViews() + 1);
    }

    /**
     * Increments clicks of Ressource
     *
     * @param Ressource $ressource
     * @return Ressource
     */
    public function incrementRessourceClicks(Ressource $ressource): Ressource
    {
        return $ressource->setClicks($ressource->getClicks() + 1);
    }
}