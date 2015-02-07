<?php

namespace Saxulum\Tests\Crud\Data\Controller;

use Saxulum\Crud\Controller\AbstractCrudController;
use Saxulum\Tests\Crud\Data\Form\SampleListType;
use Saxulum\Tests\Crud\Data\Form\SampleType;
use Saxulum\Tests\Crud\Data\Model\Sample;

class SampleController extends AbstractCrudController
{
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
     * @return SampleType
     */
    protected function crudEditFormType()
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
