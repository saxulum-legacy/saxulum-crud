<?php

namespace Saxulum\Crud\Listing\Type;

class IntegerType implements TypeInterface
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'integer';
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return '@SaxulumCrud/List/Types/integer.html.twig';
    }
}
