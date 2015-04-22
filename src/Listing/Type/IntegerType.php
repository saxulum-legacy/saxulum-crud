<?php

namespace Saxulum\Crud\Listing\Type;

class IntegerType implements TypeInterface
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'int';
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return '@SaxulumCrud/List/Types/integer.html.twig';
    }
}