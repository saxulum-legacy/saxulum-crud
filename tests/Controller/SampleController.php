<?php

namespace Saxulum\Tests\Crud\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Saxulum\Crud\Controller\CrudTrait;
use Saxulum\Tests\Crud\Form\SampleListType;
use Saxulum\Tests\Crud\Form\SampleType;
use Saxulum\Tests\Crud\Model\Sample;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class SampleController
{
    use CrudTrait;

    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var SecurityContextInterface
     */
    protected $security;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param ManagerRegistry $doctrine
     * @param PaginatorInterface $paginator
     * @param FormFactoryInterface $formFactory
     * @param UrlGeneratorInterface $urlGenerator
     * @param SecurityContextInterface $security
     * @param \Twig_Environment $twig
     */
    public function __construct(
        ManagerRegistry $doctrine,
        PaginatorInterface $paginator,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        SecurityContextInterface $security,
        \Twig_Environment $twig
    )
    {
        $this->doctrine = $doctrine;
        $this->paginator = $paginator;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
        $this->twig = $twig;
    }

    /**
     * @return ManagerRegistry
     */
    protected function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * @return PaginatorInterface
     */
    protected function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * @return FormFactoryInterface
     */
    protected function getFormFactory()
    {
        return $this->formFactory;
    }

    /**
     * @return UrlGeneratorInterface
     */
    protected function getUrlGenerator()
    {
        return $this->urlGenerator;
    }

    /**
     * @return SecurityContextInterface
     */
    protected function getSecurity()
    {
        return $this->security;
    }

    /**
     * @return \Twig_Environment
     */
    protected function getTwig()
    {
        return $this->twig;
    }

    /**
     * @return SampleListType
     */
    protected function crudListFormType()
    {
        return new SampleListType();
    }

    /**
     * @return SampleType
     */
    protected function crudCreateFormType()
    {
        return new SampleType();
    }
    
    /**
     * @return string
     * @throws \Exception
     */
    protected function crudTemplatePattern()
    {
        return '@SaxulumCrud/%s/%s.html.twig';
    }

    /**
     * @return string
     */
    protected function crudName()
    {
        return 'sample';
    }

    /**
     * @return string
     */
    protected function crudObjectClass()
    {
        return Sample::classname;
    }
}