<?php
namespace App\Service;

use App\Data\SearchData;
use App\Entity\Article;
use App\Entity\Project;
use App\Entity\Ressource;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Knp\Component\Pager\PaginatorInterface;

class SearchService
{

    /**
     * Entity Class
     *
     * @var array
     */
    private array $dataClass = [
        Article::class => 'Article',
        Ressource::class => 'Ressource',
        Project::class => 'Project',
    ];

    public function __construct(
        private PaginatorInterface $paginator,
        private EntityManagerInterface $em
    )
    {}

    /**
     * Search datas from entities and make pagination
     *
     * @param string $q query parameter
     * @param integer $page curentpage
     * @param integer $limit maximum number of data
     * @param integer $itemPerPage
     * @return SearchData
     */
    public function search(string $q, int $page, int $limit = 50, int $itemPerPage = 10): SearchData
    {
        $data = $this->getDatas($q, $limit);
        $pagination = $this->paginator->paginate($data, $page, $itemPerPage);

        return 
            (new SearchData($pagination->getItems()))
                ->setPagination($pagination)
                ->setQ($q)
                ->setCurrentPage($page)
                ->setItemsPerPage($itemPerPage)
                ->setLimit($limit)
                ->setTotalItem($pagination->getTotalItemCount())
        ;
    }

    /**
     * Search query
     *
     * @param string $q
     * @return Query
     */
    private function makeQuery(string $q): Query
    {
        $query = $this->em->createQuery('
            SELECT DISTINCT a, p, r 
            FROM App\Entity\Article a, App\Entity\Ressource r, App\Entity\Project p 
            WHERE a.title LIKE :all AND r.name LIKE :all AND p.name LIKE :all
            ORDER BY 
                CASE
                    WHEN a.title LIKE :full AND r.name LIKE :full AND p.name LIKE :full THEN 1
                    WHEN a.title LIKE :start AND r.name LIKE :start AND p.name LIKE :start THEN 2
                    WHEN a.title LIKE :end AND r.name LIKE :end AND p.name LIKE :end THEN 3
                    WHEN a.title LIKE :all AND r.name LIKE :all AND p.name LIKE :all THEN 4
                    ELSE 5
                END
        ');

        $query->setParameters([
            'all' => "%$q%",
            'start' => "%$q",
            'end' => "$q%",
            'full' => "$q"
        ]);


        return $query;
    }

    /**
     * Get data from query
     *
     * @param string $q
     * @param integer $limit
     * @return [][string, object]
     */
    private function getDatas(string $q, int $limit): array
    {
        $results = [];
        $datas = $this->makeQuery($q)->getResult();

        if(!$datas) return $datas;

        for ($i=0; $i < count($datas); $i++) { 

            $results[] = [
                $this->dataClass[$datas[$i]::class], 
                $datas[$i]
            ];

            if($i === $limit) break;
        }

        return $results;
    }

    
}