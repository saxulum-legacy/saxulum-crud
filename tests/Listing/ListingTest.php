<?php

namespace Saxulum\Tests\Crud\Listing;

use Saxulum\Crud\Listing\ListingFactory;
use Saxulum\Crud\Listing\Type\ArrayType;
use Saxulum\Crud\Listing\Type\FloatType;
use Saxulum\Crud\Listing\Type\IntegerType;
use Saxulum\Crud\Listing\Type\StringType;
use Saxulum\Tests\Crud\Data\Model\Sample;

class ListingTest extends \PHPUnit_Framework_TestCase
{
    public function testListing()
    {
        $listingFactory = new ListingFactory(array(
            new ArrayType(),
            new FloatType(),
            new IntegerType(),
            new StringType(),
        ));

        $listing = $listingFactory->create(Sample::classname);

        $listing
            ->add('id', 'integer')
            ->add('amount', 'float')
            ->add('title')
            ->add('attributes', 'array')
        ;

        $this->assertCount(4, $listing);
    }
}
