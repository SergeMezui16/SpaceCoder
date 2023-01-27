<?php
namespace App\Interface;

use App\Model\SearchItemModel;




interface SearchableInterface
{
    /**
     * Get Search Item Model for entity
     *
     * @param integer $id Id of entity
     * @return SearchItemModel
     */
    public function getSearchItem(int $id): SearchItemModel;
}