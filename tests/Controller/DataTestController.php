<?php

namespace Dynamic\Foxy\Parser\Tests\Controller;

use Dynamic\Foxy\Extension\Purchasable;
use Dynamic\Foxy\Extension\Shippable;
use Dynamic\Foxy\Model\FoxyHelper;
use Dynamic\Foxy\Orders\Model\Order;
use Dynamic\Foxy\Parser\Controller\FoxyController;
use GuzzleHttp\Client;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\DebugView;
use SilverStripe\Dev\TestOnly;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Security\Member;
use SilverStripe\Security\PasswordEncryptor;

/**
 * Class DataTestController
 * @package Dynamic\Foxy\Parser\Controller
 */
class DataTestController extends Controller implements TestOnly
{
    /**
     * @var array
     */
    private static $allowed_actions = [
        'index',
        'xml',
        'encryptedXML',
    ];

    /**
     * @var array
     */
    private static $data = [
        "TransactionDate" => "now",
        "OrderID" => "auto",
        "Email" => "auto",
        "Password" => "password",
        "OrderDetails" => [],
    ];

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \SilverStripe\ORM\ValidationException
     * @throws \SilverStripe\Security\PasswordEncryptor_NotFoundException
     */
    public function index()
    {
        $rules = Director::config()->get('rules');
        $rule = array_search(FoxyController::class, $rules);
        $myURL = Director::absoluteBaseURL() . explode('//', $rule)[0];

        $helper = new FoxyHelper();
        $myKey = $helper->config()->get('secret');

        $XMLOutput_encrypted = $this->encryptedXML();
        $XMLOutput_encrypted = urlencode($XMLOutput_encrypted);

        $client = new Client();
        $response = $client->request('POST', $myURL, [
            'form_params' => ['FoxyData' => $XMLOutput_encrypted],
        ]);

        $configString = print_r(static::config()->get('data'), true);
        /** @var DebugView $view */
        $view = Injector::inst()->create(DebugView::class);
        echo $view->renderHeader();
        echo '<div class="info">';
        echo "<h2>Config:</h2><pre>$configString</pre>";
        if ($this->getRequest()->getVar('data')) {
            //echo "<h2>Data:</h2><pre>{$xml->HTML()}</pre>";
        }
        echo "<h2>Response:</h2><pre>" . $response->getBody() . "</pre>";
        echo '<p></p>';
        echo '</div>';
        echo $view->renderFooter();
    }

    /**
     * @throws \SilverStripe\Security\PasswordEncryptor_NotFoundException
     */
    private function updateConfig()
    {
        $data = static::config()->get('data');
        $transactionDate = $data['TransactionDate'];
        static::config()->merge('data', [
            'TransactionDate' => strtotime($transactionDate),
        ]);

        $orderID = $data['OrderID'];
        if ($orderID === 'auto' || $orderID < 1) {
            $lastOrderID = 0;
            if ($lastOrder = Order::get()->sort('OrderID')->last()) {
                $lastOrderID = $lastOrder->OrderID;
            };
            static::config()->merge('data', [
                'OrderID' => $lastOrderID + 1,
            ]);
        }

        $email = $data['Email'];
        if ($email === 'auto') {
            static::config()->merge('data', [
                'Email' => $this->generateEmail(),
            ]);
        }

        $orderDetails = $data['OrderDetails'];
        if (count($orderDetails) === 0) {
            static::config()->merge('data', [
                'OrderDetails' => [
                    $this->generateOrderDetail(),
                ],
            ]);
        }

        if (!array_key_exists('Salt', $data)) {
            static::config()->merge('data', [
                'Salt' => 'faGgWXUTdZ7i42lpA6cljzKeGBeUwShBSNHECwsJmt',
            ]);
        }

        if (!array_key_exists('HashType', $data)) {
            static::config()->merge('data', [
                'HashType' => 'sha1_v2.4',
            ]);
        }

        $data = static::config()->get('data');
        if (!array_key_exists('HashedPassword', $data)) {
            $encryptor = PasswordEncryptor::create_for_algorithm($data['HashType']);
            static::config()->merge('data', [
                'HashedPassword' => $encryptor->encrypt($data['Password'], $data['Salt']),
            ]);
        }
    }

    /**
     * @return string
     */
    private function generateEmail()
    {
        $emails = Member::get()->filter([
            'Email:EndsWith' => '@example.com',
        ])->column('Email');

        if ($emails && count($emails)) {
            $email = $emails[count($emails) - 1];

            return preg_replace_callback(
                "|(\d+)|",
                function ($mathces) {
                    return ++$mathces[1];
                },
                $email
            );
        }

        return 'example0@example.com';
    }

    /**
     * @return array
     */
    private function generateOrderDetail()
    {
        return [
            'Title' => 'foo',
            'Price' => 20.00,
            'Quantity' => 1,
            'Weight' => 0.1,
            'DeliveryType' => 'shipped',
            'CategoryDescription' => 'Default cateogry',
            'CategoryCode' => 'DEFAULT',
            'Options' => ArrayList::create([
                [
                    'Name' => 'color',
                    'OptionValue' => 'blue',
                    'PriceMod' => '',
                    'WeightMod' => '',
                ],
                [
                    'Name' => 'product_id',
                    'OptionValue' => $this->getTestProduct(),
                    'PriceMod' => '',
                    'WeightMod' => '',
                ],
            ]),
        ];
    }

    /**
     * @return bool
     */
    public function getTestProduct()
    {
        $pages = SiteTree::get();
        foreach ($pages as $page) {
            if ($page->hasExtension(Purchasable::class) || $page->hasExtension(Shippable::class)) {
                return $page->ID;
            }
        }

        return false;
    }

    /**
     * @return mixed|string
     * @throws \SilverStripe\Security\PasswordEncryptor_NotFoundException
     */
    public function xml()
    {
        $this->updateConfig();
        $config = static::config()->get('data');
        $config['OrderDetails'] = ArrayList::create($config['OrderDetails']);
        $xml = $this->renderWith('TestData', $config);

        return $xml->RAW();
    }

    /**
     * @return string
     * @throws \SilverStripe\ORM\ValidationException
     * @throws \SilverStripe\Security\PasswordEncryptor_NotFoundException
     */
    public function encryptedXML()
    {
        $helper = new FoxyHelper();

        return \rc4crypt::encrypt($helper->config()->get('secret'), $this->xml());
    }
}
