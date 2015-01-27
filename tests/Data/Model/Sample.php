<?php

namespace Saxulum\Tests\Crud\Data\Model;

class Sample
{
    const classname = __CLASS__;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Sample
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
}