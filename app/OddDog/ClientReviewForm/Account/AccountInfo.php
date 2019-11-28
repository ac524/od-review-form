<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Account;


use OdReviewForm\OddDog\ClientReviewForm\FormPage\FormPage;
use OdReviewForm\OddDog\ClientReviewForm\Settings;

class AccountInfo
{
    /** @var Settings */
    public $settings;

    /** @var FormPage */
    public $formPage;

    /** @var AccountInfo */
    private static $instance;

    public static function instance( bool $autoload = true ) : self
    {
        if( null === self::$instance )

            self::$instance = new self( $autoload );

        return self::$instance;
    }

    private function __construct( bool $autoload )
    {
        if( $autoload )

            $this->load();
    }

    public function load() : self
    {
        $this->settings = Settings::getInstance();

        $this->formPage = FormPage::instance();

        return $this;
    }

    public function formPageUrl()
    {
        return $this->formPage->pageUrl();
    }
}