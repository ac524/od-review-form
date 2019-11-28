<?php


namespace OdReviewForm\OddDog\ClientReviewForm;


use OdReviewForm\Core\WpOptions\WpJsonOption;
use OdReviewForm\Core\WpOptions\WpOption;

class Settings extends WpJsonOption
{

    public $accountCode;

    public $accountToken;

    public $formPageId;

    public $businessName;

    protected $optionName = 'odrf_settings';

    /**
     * @param bool $autoload
     * @return Settings
     */
    public static function getInstance( bool $autoload = true ): WpOption
    {

        static $instance;

        if( null === $instance )

            $instance = new self();

        return $instance;

    }


    public function hasCode() : bool
    {
        return ! empty( $this->accountCode );
    }

    public function isCodeValidated() : bool
    {
        return ! empty( $this->accountToken );
    }

    public function hasValidCode() : bool
    {
        return $this->hasCode() && $this->isCodeValidated();
    }

}