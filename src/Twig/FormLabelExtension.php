<?php

namespace Saxulum\Crud\Twig;

use Saxulum\Crud\Util\Helper;
use Symfony\Component\Form\FormView;

class FormLabelExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('prepareFormLabel', array($this, 'prepareFormLabel')),
        );
    }

    /**
     * @param  FormView $formView
     * @return string
     */
    public function prepareFormLabel(FormView $formView)
    {
        $labelParts = $this->getLabelParts($formView);

        if ($labelParts[0] === '') {
            $labelParts[0] = 'form';
        }

        // hack for main form: entity_edit, will be entity.edit
        $mainFormParts = explode('_', $labelParts[0]);
        unset($labelParts[0]);

        $labelParts = array_merge($mainFormParts, array('label'), $labelParts);

        foreach ($labelParts as $i => $labelPart) {
            $labelParts[$i] = Helper::camelCaseToUnderscore($labelPart);
        }

        return implode('.', $labelParts);
    }

    /**
     * @param  FormView $formView
     * @return array
     */
    protected function getLabelParts(FormView $formView)
    {
        $labelParts = array();
        $collection = false;
        do {
            $name = $formView->vars['name'];
            if (is_numeric($name) || $name === '__name__') {
                $collection = true;
            } else {
                if ($collection) {
                    $name .= '_collection';
                }
                $labelParts[] = $name;
                $collection = false;
            }
        } while ($formView = $formView->parent);

        return array_reverse($labelParts);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'form_label_extension';
    }
}
