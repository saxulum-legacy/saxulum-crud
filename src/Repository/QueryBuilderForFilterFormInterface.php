<?php

namespace Saxulum\Crud\Repository;

use Doctrine\Common\Persistence\ObjectRepository;

interface QueryBuilderForFilterFormInterface extends ObjectRepository
{
    /**
     * @param array $filterData
     * @return object
     */
    public function getQueryBuilderForFilterForm(array $filterData = array());
}