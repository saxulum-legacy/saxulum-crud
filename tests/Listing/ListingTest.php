<?php

namespace Saxulum\Tests\Crud\Listing;

use Saxulum\Crud\Listing\Listing;
use Saxulum\Crud\Listing\ListingFactory;
use Saxulum\Crud\Listing\Type\ArrayType;
use Saxulum\Crud\Listing\Type\FloatType;
use Saxulum\Crud\Listing\Type\IntegerType;
use Saxulum\Crud\Listing\Type\StringType;

class ListingTest extends \PHPUnit_Framework_TestCase
{
    public function testListing()
    {
        $listingFactory = new ListingFactory(array(
            new ArrayType,
            new FloatType,
            new IntegerType,
            new StringType
        ));

        $listing = $listingFactory->create();

        $listing
            ->add('array', 'array')
            ->add('float', 'float')
            ->add('int', 'int')
            ->add('string')
        ;

        $this->assertCount(4, $listing);
    }

    public function testListingForClass()
    {
        $listingFactory = new ListingFactory(array(
            new ArrayType,
            new FloatType,
            new IntegerType,
            new StringType
        ));

        $listing = $listingFactory->createByClass('Saxulum\Tests\Crud\Data\Model\Sample');

        $this->assertCount(2, $listing);
    }
}