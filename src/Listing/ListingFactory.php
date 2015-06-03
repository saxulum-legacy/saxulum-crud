<?php

namespace Saxulum\Crud\Listing;

use Saxulum\Crud\Listing\Type\TypeInterface;

class ListingFactory
{
    /**
     * @var TypeInterface[]
     */
    protected $types;

    /**
     * @param TypeInterface[] $types
     */
    public function __construct(array $types)
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
    }

    /**
     * @param string $class
     *
     * @return Listing
     */
    public function create($class)
    {
        return new Listing($this->types, $class);
    }
}
