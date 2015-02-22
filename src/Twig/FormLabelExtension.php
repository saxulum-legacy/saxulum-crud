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
        $labelParts = array();
        $collection = false;
        do {
            $name = $formView->vars['name'];
            if (is_numeric($name) || $name === '__name__') {
                $collection = true;
            } else {
                if ($collection) {
                    $labelParts[] = Helper::camelCaseToUnderscore($name).'_collection';
                } else {
                    $labelParts[] = Helper::camelCaseToUnderscore(str_replace('_', '.', $name));
                }
                $collection = false;
            }
        } while ($formView = $formView->parent);

        return implode('.', array_reverse($labelParts));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'form_label_extension';
    }
}
