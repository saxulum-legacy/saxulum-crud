<?php

namespace Saxulum\Tests\Crud\Data\Controller;

use Saxulum\Crud\Controller\AbstractCrudController;
use Saxulum\Crud\Listing\Listing;
use Saxulum\Tests\Crud\Data\Form\SampleListType;
use Saxulum\Tests\Crud\Data\Form\SampleEditType;
use Saxulum\Tests\Crud\Data\Model\Sample;
use Symfony\Component\HttpFoundation\Request;

class SampleController extends AbstractCrudController
{
    /**
     * @param Request $request
     *
     * @return SampleListType
     */
    protected function crudListFormType(Request $request)
    {
        return new SampleListType();
    }

    /**
     * @return Listing
     */
    protected function crudListListing()
    {
        return parent::crudListListing()
            ->add('id', 'integer')
            ->add('title')
        ;
    }

    /**
     * @param Request $request
     * @param object $object
     *
     * @return SampleEditType
     */
    protected function crudCreateFormType(Request $request, $object)
    {
        return new SampleEditType();
    }

    /**
     * @param Request $request
     * @param object $object
     *
     * @return SampleEditType
     */
    protected function crudEditFormType(Request $request, $object)
    {
        return new SampleEditType();
    }

    /**
     * @return string
     *
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
