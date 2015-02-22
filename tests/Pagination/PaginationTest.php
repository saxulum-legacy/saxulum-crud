<?php

namespace Saxulum\Tests\Crud\Pagination;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Knp\Component\Pager\Paginator;
use Saxulum\Crud\Pagination\KnpPaginationAdapter;
use Saxulum\Crud\Pagination\PagerFantaDoctrineORMAdapter;
use Saxulum\Crud\Pagination\PaginatorInterface;

class PaginationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param PaginatorInterface $paginator
     * @dataProvider paginationProvider
     */
    public function testPrepareFormLabel(PaginatorInterface $paginator)
    {
        $this->setExpectedException(
            'Doctrine\ORM\Query\QueryException',
            '[Semantical Error] line 0, col 7 near \'s FROM Saxulum\Tests\Crud\Data\Model\Sample\': Error: \'s\' does not point to a Class.'
        );

        $pagination = $paginator->paginate($this->getQuery('SELECT s FROM Saxulum\Tests\Crud\Data\Model\Sample s'), 1, 10);

        // this is needed, cause lazy loading
        foreach ($pagination as $element) {
        }
    }

    /**
     * @param  string $dql
     * @return Query
     */
    protected function getQuery($dql)
    {
        $query = new Query($this->getEntityManager());
        $query->setDQL($dql);

        return $query;
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        $entityManagerMock = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $entityManagerMock
            ->expects($this->any())
            ->method('getConfiguration')
            ->willReturn($this->getConfiguration())
        ;

        return $entityManagerMock;
    }

    protected function getConfiguration()
    {
        $configurationMock = $this->getMock('Doctrine\ORM\Configuration');

        $configurationMock
            ->expects($this->any())
            ->method('getQueryCacheImpl')
            ->willReturn(new ArrayCache())
        ;

        return $configurationMock;
    }

    /**
     * @return array
     */
    public function paginationProvider()
    {
        return array(
            array(
                new PagerFantaDoctrineORMAdapter(),
            ),
            array(
                new KnpPaginationAdapter(new Paginator()),
            ),
        );
    }
}
