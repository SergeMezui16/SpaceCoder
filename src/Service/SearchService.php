<?php
namespace App\Service;

use App\Entity\Article;
use App\Entity\Project;
use App\Entity\Ressource;
use App\Entity\User;
use App\Model\SearchData;
use App\Model\SearchItemModel;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

class SearchService
{
    public function __construct(
        private PaginatorInterface $paginator,
        private EntityManagerInterface $manager
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
        $q = strtolower(trim($q));
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
     * @return array
     */
    private function fetch(string $q, int $limit): array
    {
        if($q == '') return []; 

        $data = $this->manager
            ->createQueryBuilder('u', 'a', 'r', 'p')
            ->select('u', 'a', 'r', 'p')

            ->from(Article::class, 'a')
            ->from(Ressource::class, 'r')
            ->from(Project::class, 'p')
            ->from(User::class, 'u')

            ->orWhere('u.pseudo LIKE :param')

            ->orWhere('r.name LIKE :param')
            ->orWhere('r.description LIKE :param')

            ->orWhere('p.name LIKE :param')
            ->orWhere('p.description LIKE :param')

            ->orWhere('a.title LIKE :param')
            ->orWhere('a.subject LIKE :param')
            ->orWhere('a.description LIKE :param')
            ->orWhere('a.content LIKE :param')
            ->andWhere('a.publishedAt <= :now')

            ->setMaxResults($limit)

            ->setParameter('param', "%$q%")
            ->setParameter('now', new \DateTimeImmutable())

            ->getQuery()
            ->getResult()
        ;

        return $data;
    }

    /**
     * Get data from query
     *
     * @param string $q
     * @param integer $limit
     * @return SearchItemModel[]
     */
    private function getDatas(string $q, int $limit): array
    {
        $datas = $this->fetch($q, $limit);

        if(empty($datas)) return $datas;

        $results = [];
        $filtered = [];

        // Get pertinance
        for ($i = 0; $i < count($datas); $i++) {

            $result = $datas[$i]->getSearchItem($i);
            if (preg_match('/([a-zA-Z0-9]?)+(' . $q . ')([a-zA-Z0-9]?)+/i', $result->getTitle())) {
                $results[] = [
                    preg_match_all('/([a-zA-Z0-9]?)+(' . $q . ')([a-zA-Z0-9]?)+/i', $result->getTitle()),
                    $result
                ];
            }
            $result->setDescription(str_replace($q, '<span class="bling">'.$q.'</span>', strtolower($result->getDescription())))->setTitle(str_replace($q, '<span class="bling">'.$q.'</span>', strtolower($result->getTitle())));
        }

        // Oredered by pertinance
        for ($i=0; $i < count($results); $i++) { 

            for ($j=0; $j < count($results); $j++) { 
                $p = 0;
                if($results[$i][0] > $results[$j][0])
                {
                    $p = $results[$j];
                    $results[$j] = $results[$i];
                    $results[$i] = $p;
                }
            }
        }
        // Render
        foreach ($results as $el) {
            $filtered[] = $el[1];
        }

        return $filtered;
    }
}