<?php

namespace Saxulum\Crud\Listing\Type;

class FloatType implements TypeInterface
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'float';
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return '@SaxulumCrud/List/Types/float.html.twig';
    }
}