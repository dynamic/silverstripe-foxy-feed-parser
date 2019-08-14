<?php

namespace Dynamic\Foxy\Parser\Tests;

use Dynamic\Foxy\Model\FoxyHelper;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Security\Member;
use SilverStripe\Security\PasswordEncryptor;

class FoxyXMLFeedFactory
{
    use Configurable;
    use Injectable;

    /**
     * @var Controller
     */
    private $controller;

    /**
     * @var array
     */
    private $feed_array = [];


    /**
     * @var array
     */
    private static $feed_array_config = [];

    /**
     * FoxyXMLFeedFactory constructor.
     * @param Controller $controller
     * @param array $feedData
     */
    public function __construct(Controller $controller, $feedData = [])
    {
        $this->setController($controller);

        if (is_array($feedData) && !empty($feedData)) {
            $this->setFeedArray($feedData);
        }
    }

    /**
     * @return string
     */
    public static function generate_email()
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
     * @param string $algorithm
     * @param string $password
     * @param string $salt
     * @return String
     * @throws \SilverStripe\Security\PasswordEncryptor_NotFoundException
     */
    public static function get_hashed_password($algorithm, $password, $salt)
    {
        $encryptor = PasswordEncryptor::create_for_algorithm($algorithm);

        return $encryptor->encrypt($password, $salt);
    }

    /**
     * @param $controller
     * @return $this
     */
    protected function setController($controller)
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * @return Controller
     */
    protected function getController()
    {
        return $this->controller;
    }

    /**
     * @param array $feedData
     * @return $this
     */
    public function setFeedArray($feedData = [])
    {
        if (is_array($feedData) && !empty($feedData)) {
            $this->feed_array = $feedData;
        } else {
            $this->feed_array = $this->config()->get('feed_array_config');
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getFeedArray()
    {
        return $this->feed_array;
    }

    /**
     * @return mixed
     */
    public function getXML()
    {
        $xml = Controller::curr()->renderWith('TestData', $this->getFeedArray());

        return $xml->RAW();
    }

    /**
     * @return string
     * @throws \SilverStripe\ORM\ValidationException
     * @throws \SilverStripe\Security\PasswordEncryptor_NotFoundException
     */
    public function encryptedXML()
    {
        $helper = FoxyHelper::singleton();

        return \rc4crypt::encrypt($helper->config()->get('secret'), $this->getXML());
    }
}
