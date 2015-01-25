<?php

namespace Saxulum\Tests\Crud\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class CrudTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testList()
    {
        $controller = $this->initializeController();
    }

    /**
     * @return SampleController
     */
    protected function initializeController()
    {
        return new SampleController(
            $this->getDoctrine(),
            $this->getPaginator(),
            $this->getFormFactory(),
            $this->getUrlGenerator(),
            $this->getSecurity(),
            $this->getTwig()
        );
    }

    /**
     * @return ManagerRegistry
     */
    protected function getDoctrine()
    {
        return $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
    }

    /**
     * @return PaginatorInterface
     */
    protected function getPaginator()
    {
        return $this->getMock('Knp\Component\Pager\PaginatorInterface');
    }

    /**
     * @return FormFactoryInterface
     */
    protected function getFormFactory()
    {
        return $this->getMock('Symfony\Component\Form\FormFactoryInterface');
    }

    /**
     * @return UrlGeneratorInterface
     */
    protected function getUrlGenerator()
    {
        return $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
    }

    /**
     * @return SecurityContextInterface
     */
    protected function getSecurity()
    {
        return $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
    }

    /**
     * @return \Twig_Environment
     */
    protected function getTwig()
    {
        return $this->getMock('\Twig_Environment');
    }
}