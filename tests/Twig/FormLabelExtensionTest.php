<?php

namespace Saxulum\Tests\Crud\Twig;

use Saxulum\Crud\Twig\FormLabelExtension;
use Saxulum\Tests\Crud\Data\Form\SampleListType;
use Saxulum\Tests\Crud\Data\Form\SampleEditType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeFactory;

class FormLabelExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string            $formType
     * @param string            $expectName
     * @dataProvider prepareFormLabelProvider
     */
    public function testPrepareFormLabel($formType, $expectName)
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
                'Saxulum\Tests\Crud\Data\Form\SampleListType',
                'sample.list.label',
            ),
            array(
                'Saxulum\Tests\Crud\Data\Form\SampleEditType',
                'sample.edit.label',
            ),
        );
    }
}
