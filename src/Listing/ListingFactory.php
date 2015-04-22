<?php

namespace Saxulum\Crud\Listing;

use Saxulum\Crud\Listing\Type\TypeInterface;

class ListingFactory
{
    /**
     * @var TypeInterface[]
     */
    protected $types;

    const PATTERN_VAR = '/\@var[ |\t]+([^\s]+)/';

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
     * @return Listing
     */
    public function create()
    {
        return new Listing($this->types);
    }

    /**
     * @param string $class
     * @return Listing
     */
    public function createByClass($class)
    {
        if(!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf(
                'There is no class with name %s, or cant be autoloaded',
                $class
            ));
        }

        $listing = new Listing($this->types);

        $reflectionClass = new \ReflectionClass($class);
        foreach($reflectionClass->getProperties() as $reflectionProperty) {
            if(!$docComment = $reflectionProperty->getDocComment()) {
                continue;
            }
            $match = array();
            if(1 !== preg_match(self::PATTERN_VAR, $docComment, $match)) {
                continue;
            }
            $varParts = explode('|', $match[1]);
            foreach($varParts as $varPart) {
                if(isset($this->types[$varPart])) {
                    $listing->add($reflectionProperty->getName(), $varPart);
                    break;
                }
            }
        }

        return $listing;
    }
}