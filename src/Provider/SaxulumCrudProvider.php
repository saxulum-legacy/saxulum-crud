<?php

namespace Saxulum\Crud\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Saxulum\Crud\Listing\ListingFactory;
use Saxulum\Crud\Listing\Type\ArrayType;
use Saxulum\Crud\Listing\Type\FloatType;
use Saxulum\Crud\Listing\Type\IntegerType;
use Saxulum\Crud\Listing\Type\StringType;

class SaxulumCrudProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['saxulum.crud.listing.types'] = function(){
            return array(
                new ArrayType(),
                new FloatType(),
                new IntegerType(),
                new StringType(),
            );
        };

        $pimple['saxulum.crud.listing.factory'] = function() use ($pimple) {
            return new ListingFactory($pimple['saxulum.crud.listing.types']);
        };
    }
}
