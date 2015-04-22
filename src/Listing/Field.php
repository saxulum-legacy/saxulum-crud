<?php

namespace Saxulum\Crud\Listing;

class Field
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param string $name
     * @param string $type
     * @param string $template
     * @param array $options
     */
    public function __construct($name, $type, $template, array $options)
    {
        $this->name = $name;
        $this->type = $type;
        $this->template = $template;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}