<?php

namespace Dynamic\Foxy\Parser\Tests\Product;

use Dynamic\Foxy\Extension\Shippable;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Dev\TestOnly;

/**
 * Class TestProduct
 * @package Dynamic\Foxy\Parser\Tests\Product
 */
class FoxyFeedTestProduct extends SiteTree implements TestOnly
{
    /**
     * @var string
     */
    private static $table_name = 'FoxyFeedTestProduct';

    /**
     * @var array
     */
    private static $extensions = [
        Shippable::class,
    ];
}
