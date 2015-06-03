<?php

namespace Saxulum\Tests\Crud\Listing;

use Pimple\Container;
use Saxulum\Crud\Listing\ListingFactory;
use Saxulum\Crud\Listing\Type\ArrayType;
use Saxulum\Crud\Listing\Type\FloatType;
use Saxulum\Crud\Listing\Type\IntegerType;
use Saxulum\Crud\Listing\Type\StringType;
use Saxulum\Crud\Provider\SaxulumCrudProvider;
use Saxulum\Tests\Crud\Data\Model\Sample;

class ListingTest extends \PHPUnit_Framework_TestCase
{
    public function testListing()
    {
        $container = new Container();
        $container->register(new SaxulumCrudProvider());

        /** @var ListingFactory $listingFactory */
        $listingFactory = $container['saxulum.crud.listing.factory'];

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
