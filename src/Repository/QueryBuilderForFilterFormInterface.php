<?php

namespace Saxulum\Crud\Repository;

use Doctrine\Common\Persistence\ObjectRepository;

interface QueryBuilderForFilterFormInterface extends ObjectRepository
{
    const interfacename = __CLASS__;

    /**
     * @param array $filterData
     * @return object
     */
    public function getQueryBuilderForFilterForm(array $filterData = array());
}
