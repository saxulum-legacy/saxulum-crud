<?php

namespace Saxulum\Crud\Listing\Type;

class StringType implements TypeInterface
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'string';
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return '@SaxulumCrud/List/Types/string.html.twig';
    }
}
