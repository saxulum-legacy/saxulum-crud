<?php

namespace Saxulum\Crud\Pagination;

use Knp\Component\Pager\PaginatorInterface as KnpPaginatorInterface;

class KnpPaginationAdapter implements PaginatorInterface
{
    /**
     * @var KnpPaginatorInterface
     */
    protected $paginator;

    /**
     * @param KnpPaginatorInterface $paginator
     */
    public function __construct(KnpPaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @param  object $target
     * @param  int    $page
     * @param  int    $limit
     * @param  array  $options
     * @return \Traversable
     */
    public function paginate($target, $page = 1, $limit = 10, array $options = array())
    {
        return $this->paginator->paginate($target, $page, $limit, $options);
    }
}
