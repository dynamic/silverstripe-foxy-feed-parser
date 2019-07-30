<?php

namespace Dynamic\Foxy\Parser\Tests\Foxy;

use Dynamic\Foxy\Model\FoxyHelper;
use Dynamic\Foxy\Parser\Foxy\Transaction;
use Dynamic\Foxy\Parser\Tests\Controller\DataTestController;
use Dynamic\Foxy\Parser\Tests\Product\FoxyFeedTestProduct;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;

/**
 * Class TransactionTest
 * @package Dynamic\Foxy\Parser\Tests\Foxy
 */
class TransactionTest extends FunctionalTest
{
    /**
     * @var string
     */
    protected static $fixture_file = '../fixtures.yml';

    /**
     * @var array
     */
    protected static $extra_dataobjects = [
        FoxyFeedTestProduct::class,
    ];

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();

        $defaultKey = Config::inst()->get(FoxyHelper::class, 'secret');
        Config::modify()->set(FoxyHelper::class, 'secret', 'abc123');
    }

    /**
     *
     */
    public function testTransactionConstruct()
    {
        $response = $this->get('/foxytest/xml');

        $this->expectException(\ArgumentCountError::class);
        Transaction::create();

        $this->assertInstanceOf(Transaction::class, Transaction::create($response));
    }

    /**
     * @throws \SilverStripe\ORM\ValidationException
     * @throws \SilverStripe\Security\PasswordEncryptor_NotFoundException
     */
    public function testGetParsedTransactionData()
    {
        $response = DataTestController::singleton()->encryptedXML();

        $this->assertInstanceOf(ArrayData::class, Transaction::create($response)->getParsedTransactionData());
    }

    /**
     * @throws \SilverStripe\ORM\ValidationException
     * @throws \SilverStripe\Security\PasswordEncryptor_NotFoundException
     */
    public function testAttributesExist()
    {
        $response = DataTestController::singleton()->encryptedXML();
        $transaction = Transaction::create($response)->getParsedTransactionData();

        $this->assertInstanceOf(ArrayList::class, $transaction->products);
        $this->assertInstanceOf(ArrayList::class, $transaction->discounts);
    }
}
