<?php

namespace Saxulum\Crud\Pagination;

interface PaginatorInterface
{
    /**
     * @param  object $target
     * @param  int    $page
     * @param  int    $limit
     * @param  array  $options
     * @return mixed
     */
    public function paginate($target, $page = 1, $limit = 10, array $options = array());
}
