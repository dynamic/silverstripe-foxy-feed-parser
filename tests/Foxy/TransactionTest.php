<?php

namespace Dynamic\Foxy\Parser\Tests\Foxy;

use Dynamic\Foxy\Model\FoxyHelper;
use Dynamic\Foxy\Parser\Foxy\Transaction;
use Dynamic\Foxy\Parser\Tests\Controller\DataTestController;
use Dynamic\Foxy\Parser\Tests\FoxyXMLFeedFactory;
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
     * @throws \SilverStripe\Security\PasswordEncryptor_NotFoundException
     */
    protected static function get_foxy_data()
    {
        $password = 'password';
        $hashType = 'sha1_v2.4';
        $salt = 'faGgWXUTdZ7i42lpA6cljzKeGBeUwShBSNHECwsJmt';

        return [
            "TransactionDate" => strtotime("now"),
            "OrderID" => rand(0, 5000),
            "Email" => FoxyXMLFeedFactory::generate_email(),
            "Password" => FoxyXMLFeedFactory::get_hashed_password($hashType, $password, $salt),
            'Salt' => $salt,
            'HashType' => $hashType,
            "OrderDetails" => ArrayData::create([
                'Title' => 'foo',
                'Price' => 20.00,
                'Quantity' => 1,
                'Weight' => 0.1,
                'DeliveryType' => 'shipped',
                'CategoryDescription' => 'Default cateogry',
                'CategoryCode' => 'DEFAULT',
                'Options' => [
                    [
                        'Name' => 'color',
                        'OptionValue' => 'blue',
                        'PriceMod' => '',
                        'WeightMod' => '',
                    ],
                    [
                        'Name' => 'product_id',
                        'OptionValue' => 5,
                        'PriceMod' => '',
                        'WeightMod' => '',
                    ],
                ],
            ]),
        ];
    }

    /**
     * @throws \Exception
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        Config::modify()->set(FoxyHelper::class, 'secret', 'abc123');
        Config::modify()->merge(DataTestController::class, 'allowed_actions', ['index', 'xml', 'encryptedXML']);
        Config::modify()->set(DataTestController::class, 'data', static::get_foxy_data());
        Config::modify()->set(DataTestController::class, 'run_config_update', false);
    }

    /**
     *
     */
    public function testTransactionConstruct()
    {
        $response = $this->get('/foxytest/xml');

        if (PHP_VERSION_ID < 70100) {
            $this->markTestSkipped('PHPUnit_Framework_Error_Warning exception is thrown for legacy PHP versions only');
        } else {
            $this->expectException(\ArgumentCountError::class);
        }

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
