<?php

namespace Saxulum\Crud\Listing\Type;

class BooleanType implements TypeInterface
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'boolean';
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return '@SaxulumCrud/List/Types/boolean.html.twig';
    }
}
