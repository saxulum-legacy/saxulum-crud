<?php

namespace Saxulum\Crud\Pagination;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class PagerFantaDoctrineORMAdapter implements PaginatorInterface
{
    /**
     * @param  object $target
     * @param  int $page
     * @param  int $limit
     * @param  array $options
     * @return \Traversable
     */
    public function paginate($target, $page = 1, $limit = 10, array $options = array())
    {
        $adapter = new DoctrineORMAdapter($target);
        $pagerFanta = new Pagerfanta($adapter);
        $pagerFanta->setMaxPerPage($limit);
        $pagerFanta->setCurrentPage($page);

        return $pagerFanta;
    }
}