<?php

namespace Saxulum\Tests\Crud\Twig;

use Saxulum\Crud\Twig\FormLabelExtension;
use Saxulum\Tests\Crud\Data\Form\SampleListType;
use Saxulum\Tests\Crud\Data\Form\SampleType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeFactory;

class FormLabelExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param FormTypeInterface $formType
     * @param string            $expectName
     * @dataProvider prepareFormLabelProvider
     */
    public function testPrepareFormLabel(FormTypeInterface $formType, $expectName)
    {
        $formFactory = $this->getFormFactory();
        $form = $formFactory->create($formType);
        $formView = $form->createView();

        $extension = new FormLabelExtension();

        $this->assertEquals($expectName, $extension->prepareFormLabel($formView));
    }

    /**
     * @return FormFactoryInterface
     */
    protected function getFormFactory()
    {
        return Forms::createFormFactoryBuilder()
            ->setResolvedTypeFactory(new ResolvedFormTypeFactory())
            ->getFormFactory();
    }

    /**
     * @return array
     */
    public function prepareFormLabelProvider()
    {
        return array(
            array(
                new SampleListType(),
                'sample.list.label',
            ),
            array(
                new SampleType(),
                'sample.edit.label',
            ),
        );
    }
}
