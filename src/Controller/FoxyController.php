<?php

namespace Dynamic\Foxy\Parser\Controller;

use Dynamic\Foxy\Parser\Foxy\Transaction;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\ValidationException;

/**
 * Class FoxyController
 * @package Dynamic\Foxy\Parser\Controller
 */
class FoxyController extends Controller
{
    /**
     *
     */
    public const URLSEGMENT = 'foxy';

    /**
     * @var array
     */
    private static $allowed_actions = [
        'index',
    ];

    /**
     * @return string
     */
    public function getURLSegment()
    {
        return self::URLSEGMENT;
    }

    /**
     * @return string
     * @throws ValidationException
     */
    public function index()
    {
        $request = $this->getRequest();
        if ($request->postVar('FoxyData') || $request->postVar('FoxySubscriptionData')) {
            $this->processFoxyRequest($request);

            return 'foxy';
        }

        return 'No FoxyData or FoxySubscriptionData received.';
    }

    /**
     * Process a request after a transaction is completed via Foxy
     *
     * @param HTTPRequest $request
     * @throws ValidationException
     */
    protected function processFoxyRequest(HTTPRequest $request)
    {
        $encryptedData = $request->postVar('FoxyData')
            ? urldecode($request->postVar('FoxyData'))
            : urldecode($request->postVar('FoxySubscriptionData'));
        $this->parseFeedData($encryptedData);

        $encryptedData = urlencode($encryptedData);

        $this->extend('addIntegrations', $encryptedData);
    }

    /**
     * Parse the XML data feed from Foxy to a SimpleXMLElement object
     *
     * @param $encryptedData
     * @param $decryptedData
     * @throws ValidationException
     */
    private function parseFeedData($encryptedData)
    {
        $transaction = Transaction::create($encryptedData);

        $this->extend('doAdditionalParse', $transaction);
    }
}
