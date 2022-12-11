<?php

namespace App\Service\Entity;


use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;

class ProjectService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {}

    public function incrementVisit(Project $project)
    {
        $project->setVisit($project->getVisit() + 1);

        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }
}