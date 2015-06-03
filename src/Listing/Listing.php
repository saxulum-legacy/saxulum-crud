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
     * @var \ReflectionClass
     */
    protected $reflectionClass;

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
     * @param string          $class
     */
    public function __construct(array $types, $class)
    {
        foreach ($types as $type) {
            if (!$type instanceof TypeInterface) {
                throw new \InvalidArgumentException(sprintf(
                    'Type must be an instance of %s, %s given!',
                    'Saxulum\Crud\Listing\Type\TypeInterface',
                    is_object($type) ? get_class($type) : gettype($type)
                ));
            }
            $this->types[$type->getName()] = $type;
        }
        $this->reflectionClass = new \ReflectionClass($class);
    }

    /**
     * @param string $name
     * @param string $type
     * @param array  $options
     *
     * @return $this
     */
    public function add($name, $type = 'string', array $options = array())
    {
        $getMethod = 'get'.ucfirst($name);
        $isMethod = 'is'.ucfirst($name);

        $isAccessible = false;
        if (($this->reflectionClass->hasProperty($name) && $this->reflectionClass->getProperty($name)->isPublic()) ||
           ($this->reflectionClass->hasMethod($getMethod) && $this->reflectionClass->getMethod($getMethod)->isPublic()) ||
           ($this->reflectionClass->hasMethod($isMethod) && $this->reflectionClass->getMethod($isMethod)->isPublic())) {
            $isAccessible = true;
        }

        if (!$isAccessible) {
            throw new \InvalidArgumentException(sprintf('There is no public property, get or is method for: %s!', $name));
        }

        if (!$type instanceof TypeInterface) {
            $type = $this->getType($type);
        }

        $this->fields[] = new Field(
            $name,
            $type->getName(),
            $type->getTemplate(),
            $options
        );

        return $this;
    }

    /**
     * @param string $type
     *
     * @return TypeInterface
     *
     * @throws \InvalidArgumentException
     */
    protected function getType($type)
    {
        if (!isset($this->types[$type])) {
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
        return array_key_exists($this->fieldPointer, $this->fields);
    }

    /**
     */
    public function rewind()
    {
        $this->fieldPointer = 0;
    }
}
