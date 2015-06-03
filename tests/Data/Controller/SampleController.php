<?php

namespace Saxulum\Tests\Crud\Data\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Saxulum\Crud\Controller\CrudTrait;
use Saxulum\Tests\Crud\Data\Form\SampleListType;
use Saxulum\Tests\Crud\Data\Form\SampleType;
use Saxulum\Tests\Crud\Data\Model\Sample;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class SampleController
{
    use CrudTrait;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param ManagerRegistry          $doctrine
     * @param FormFactoryInterface     $formFactory
     * @param PaginatorInterface       $paginator
     * @param UrlGeneratorInterface    $urlGenerator
     * @param \Twig_Environment        $twig
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        ManagerRegistry $doctrine,
        FormFactoryInterface $formFactory = null,
        PaginatorInterface $paginator = null,
        UrlGeneratorInterface $urlGenerator = null,
        \Twig_Environment $twig = null
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->doctrine = $doctrine;
        $this->paginator = $paginator;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
    }

    /**
     * @return AuthorizationCheckerInterface
     */
    protected function crudAuthorizationChecker()
    {
        return $this->authorizationChecker;
    }

    /**
     * @return ManagerRegistry
     */
    protected function crudDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * @return PaginatorInterface
     */
    protected function crudPaginator()
    {
        return $this->paginator;
    }

    /**
     * @return FormFactoryInterface
     */
    protected function crudFormFactory()
    {
        return $this->formFactory;
    }

    /**
     * @return UrlGeneratorInterface
     */
    protected function crudUrlGenerator()
    {
        return $this->urlGenerator;
    }

    /**
     * @return \Twig_Environment
     */
    protected function crudTwig()
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
     * @param Sample $object
     * @return SampleType
     */
    protected function crudCreateFormType($object)
    {
        return new SampleType();
    }

    /**
     * @param Sample $object
     * @return SampleType
     */
    protected function crudEditFormType($object)
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
