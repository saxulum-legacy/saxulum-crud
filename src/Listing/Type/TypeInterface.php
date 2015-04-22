<?php

namespace Saxulum\Crud\Listing\Type;

interface TypeInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getTemplate();
}