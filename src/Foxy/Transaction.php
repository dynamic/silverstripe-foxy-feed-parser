<?php

namespace Dynamic\Foxy\Parser\Foxy;

use Dynamic\Foxy\Model\FoxyHelper;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\ValidationException;
use SilverStripe\View\ArrayData;

/**
 * Class Transaction
 * @package Dynamic\Foxy\Parser\Foxy
 */
class Transaction
{
    use Injectable;
    use Configurable;

    /**
     * @var string Encrypted response from Foxy
     */
    private $encrypted_data;

    /**
     * @var "Foxy.io transaction xml record"
     */
    private $transaction;

    /**
     * @var ArrayData
     */
    private $transaction_data;

    /**
     * @var ArrayList
     */
    private $discount_data;

    /**
     * @var ArrayList
     */
    private $product_data;

    /**
     * Transaction constructor.
     * @param $data string encrypted foxy response data from the xml data feed
     * @throws ValidationException
     */
    public function __construct($data)
    {
        $this->setEncryptedData($data);
        $this->setTransaction($data);
    }

    /**
     * @param $data
     * @return $this
     */
    public function setEncryptedData($data)
    {
        $this->encrypted_data = $data;

        return $this;
    }

    /**
     * @return string
     */
    public function getEncryptedData()
    {
        return $this->encrypted_data;
    }

    /**
     * Set the decrypted transaction data to use.
     *
     * @param $data
     * @return $this
     * @throws ValidationException
     */
    public function setTransaction($data)
    {
        $decryptedData = $this->getDecryptedData($data);

        foreach ($decryptedData->transactions->transaction as $transaction) {
            if ($this->hasID($transaction)) {
                $this->transaction = $transaction;
                break;
            }
        }

        if (!$this->transaction) {
            $this->transaction = false;
        }

        return $this;
    }

    /**
     * Check if there is a transaction ID for the given decrypted transaction data.
     *
     * @param $transaction
     * @return bool
     */
    protected function hasID($transaction)
    {
        return (int)$transaction->id > 0;
    }

    /**
     * Return the decrypted transaction xml data.
     *
     * @return mixed
     */
    protected function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Return an ArrayData containing transaction, products and discounts data.
     *
     * @return ArrayData
     */
    public function getParsedTransactionData()
    {
        /**
         *
         */
        return ArrayData::create([
            'transaction' => $this->getTransactionData(),
            'products' => $this->getProductData(),
            'discounts' => $this->getDiscountData(),
        ]);
    }

    /**
     * Set the base transaction data in an ArrayData object.
     *
     * @return $this
     */
    private function setTransactionData()
    {
        $this->transaction_data = $this->getObject(
            $this->getTransaction(),
            $this->config()->get('transaction_mapping')
        );

        return $this;
    }

    /**
     * Return base transaction data.
     *
     * @return ArrayData
     */
    protected function getTransactionData()
    {
        if (!$this->transaction_data instanceof ArrayData) {
            $this->setTransactionData();
        }

        return $this->transaction_data;
    }

    /**
     * Return discounts data from Foxy.io xml data feed if any.
     *
     * @return ArrayList
     */
    protected function getDiscountData()
    {
        if (!$this->discount_data instanceof ArrayList) {
            $this->setDiscountData();
        }

        return $this->discount_data;
    }

    /**
     * Set an ArrayList with possible discount data as ArrayData objects.
     *
     * @return $this
     */
    protected function setDiscountData()
    {
        $discounts = $this->getTransaction()->discounts;
        $discountsList = ArrayList::create();

        foreach ($discounts as $discount) {
            $discountsList->push($this->getObject($discount, $this->config()->get('discounts_mapping')));
        }

        $this->discount_data = $discountsList;

        return $this;
    }

    /**
     * Return transaction_details data from Foxy.io xml data feed if any.
     *
     * @return ArrayList
     */
    protected function getProductData()
    {
        if (!$this->product_data instanceof ArrayList) {
            $this->setProductData();
        }

        return $this->product_data;
    }

    /**
     * Set an ArrayList with possible transaction_detail data as ArrayData objects.
     *
     * @return $this
     */
    protected function setProductData()
    {
        $details = $this->getTransaction()->transaction_details->transaction_detail;
        $detailsList = ArrayList::create();

        foreach ($details as $detail) {
            $product = $this->getObject($detail, $this->config()->get('transaction_detail_mapping'));

            $product->transaction_detail_options =
                $this->getProductOptions($detail->transaction_detail_options->transaction_detail_option);

            $detailsList->push($product);
        }

        $this->product_data = $detailsList;

        return $this;
    }

    /**
     * Returns an ArrayList containing possible transaction_detail_option data as ArrayData objects.
     *
     * @param $data
     * @return ArrayList
     */
    protected function getProductOptions($data)
    {
        $options = ArrayList::create();

        foreach ($data as $option) {
            $options->push($this->getObject($option, $this->config()->get('transaction_detail_option_mapping')));
        }

        return $options;
    }


    /**
     * Returns an ArrayData object based on the given iterable data and a key/val config array. Used
     * to type hint data from the Foxy.io xml data feed.
     *
     * @param $data
     * @param array $config
     * @return ArrayData
     */
    protected function getObject($data, $config = [])
    {
        $dataArray = [];

        foreach ($config as $name => $type) {
            switch ($type) {
                case 'int':
                    $dataArray[$name] = (int)$data->{$name};
                    break;
                case 'float':
                    $dataArray[$name] = (float)$data->{$name};
                    break;
                case 'string':
                default:
                    $dataArray[$name] = (string)$data->{$name};
                    break;
            }
        }

        return ArrayData::create($dataArray);
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return $this->getTransaction() != false && !empty($this->getTransaction());
    }

    /**
     * @param $data
     * @return \SimpleXMLElement
     * @throws \SilverStripe\ORM\ValidationException
     */
    private function getDecryptedData($data)
    {
        $helper = new FoxyHelper();

        return new \SimpleXMLElement(\rc4crypt::decrypt($helper->config()->get('secret'), $data));
    }
}
