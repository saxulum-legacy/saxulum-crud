<?php

namespace Saxulum\Crud\Listing\Type;

class ArrayType implements TypeInterface
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'array';
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return '@SaxulumCrud/List/Types/array.html.twig';
    }
}