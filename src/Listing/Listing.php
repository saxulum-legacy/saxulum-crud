<?php

namespace Saxulum\Crud\Listing;

use Saxulum\Crud\Listing\Type\TypeInterface;

class Listing implements \Iterator
{
    /**
     * @var TypeInterface[]
     */
    protected $types;

    /**
     * @var Field[]
     */
    protected $fields;

    /**
     * @var int
     */
    protected $fieldPointer = 0;

    /**
     * @param TypeInterface[] $types
     */
    public function __construct(array $types)
    {
        foreach($types as $type) {
            if(!$type instanceof TypeInterface) {
                throw new \InvalidArgumentException(sprintf(
                    'Type must be an instance of %s, %s given!',
                    'Saxulum\Crud\Listing\Type\TypeInterface',
                    is_object($type) ? get_class($type) : gettype($type)
                ));
            }
            $this->types[$type->getName()] = $type;
        }
    }

    /**
     * @param string $name
     * @param string $type
     * @param array $options
     * @return $this
     */
    public function add($name, $type = 'string', array $options = array())
    {
        if($type instanceof TypeInterface) {
            $this->fields[] = new Field(
                $name,
                $type->getName(),
                $type->getTemplate(),
                $options
            );

            return $this;
        }

        $this->fields[] = new Field(
            $name,
            $this->getType($type)->getName(),
            $this->getType($type)->getTemplate(),
            $options
        );

        return $this;
    }

    /**
     * @param string $type
     * @return TypeInterface
     * @throws \InvalidArgumentException
     */
    protected function getType($type)
    {
        if(!isset($this->types[$type])) {
            throw new \InvalidArgumentException(sprintf('There is no type with name %s', $type));
        }

        return $this->types[$type];
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return $this->fields[$this->fieldPointer];
    }

    /**
     * @return void
     */
    public function next()
    {
        $this->fieldPointer++;
    }

    /**
     * @return string
     */
    public function key()
    {
        return $this->fields[$this->fieldPointer]->getName();
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return array_key_exists($this->fieldPointer , $this->fields);
    }

    /**
     * @return void
     */
    public function rewind()
    {
        $this->fieldPointer = 0;
    }
}