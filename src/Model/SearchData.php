<?php

namespace App\Model;

use Knp\Component\Pager\Pagination\PaginationInterface;

class SearchData
{
    private PaginationInterface $pagination;

    private iterable $data = [];

    private ?string $q = null;

    private int $currentPage = 1;

    private int $itemsPerPage = 10;

    private int $totalItem;

    private int $limit;


    public function __construct(iterable $data)
    {
        $this->data = $data;
    }


    /**
     * Get the value of limit
     */ 
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set the value of limit
     *
     * @return  self
     */ 
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get the value of itemsPerPage
     */ 
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * Set the value of itemsPerPage
     *
     * @return  self
     */ 
    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;

        return $this;
    }

    /**
     * Get the value of curentPage
     */ 
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Set the value of curentPage
     *
     * @return  self
     */ 
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * Get the value of q
     */ 
    public function getQ()
    {
        return $this->q;
    }

    /**
     * Set the value of q
     *
     * @return  self
     */ 
    public function setQ($q)
    {
        $this->q = $q;

        return $this;
    }

    /**
     * Get the value of data
     */ 
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get the value of pagination
     */ 
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * Set the value of pagination
     *
     * @return  self
     */ 
    public function setPagination($pagination)
    {
        $this->pagination = $pagination;

        return $this;
    }

    /**
     * Get the value of totalItem
     */ 
    public function getTotalItem()
    {
        return $this->totalItem;
    }

    /**
     * Set the value of totalItem
     *
     * @return  self
     */ 
    public function setTotalItem($totalItem)
    {
        $this->totalItem = $totalItem;

        return $this;
    }
}
