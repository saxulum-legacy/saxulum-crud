<?php

namespace Saxulum\Crud\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Saxulum\Crud\Listing\ListingFactory;
use Saxulum\Crud\Pagination\PaginatorInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

abstract class AbstractCrudController
{
    use CrudTrait;

    /**
     * @var SecurityContextInterface
     */
    protected $security;

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
     * @var ListingFactory
     */
    protected $listingFactory;

    /**
     * @param SecurityContextInterface $security
     * @param ManagerRegistry          $doctrine
     * @param FormFactoryInterface     $formFactory
     * @param PaginatorInterface       $paginator
     * @param UrlGeneratorInterface    $urlGenerator
     * @param \Twig_Environment        $twig
     * @param ListingFactory $listingFactory
     */
    public function __construct(
        SecurityContextInterface $security,
        ManagerRegistry $doctrine,
        FormFactoryInterface $formFactory = null,
        PaginatorInterface $paginator = null,
        UrlGeneratorInterface $urlGenerator = null,
        \Twig_Environment $twig = null,
        ListingFactory $listingFactory = null
    ) {
        $this->doctrine = $doctrine;
        $this->paginator = $paginator;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
        $this->twig = $twig;
        $this->listingFactory = $listingFactory;
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
     * @return SecurityContextInterface
     */
    protected function crudSecurity()
    {
        return $this->security;
    }

    /**
     * @return \Twig_Environment
     */
    protected function crudTwig()
    {
        return $this->twig;
    }

    /**
     * @return ListingFactory
     */
    protected function crudListingFactory()
    {
        return $this->listingFactory;
    }
}
