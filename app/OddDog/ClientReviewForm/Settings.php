<?php


namespace OdReviewForm\OddDog\ClientReviewForm;


use OdReviewForm\Core\WpOptions\WpJsonOption;
use OdReviewForm\Core\WpOptions\WpOption;
use OdReviewForm\OddDog\FormsApiClient\ReviewFormsClient;

class Settings extends WpJsonOption
{

    public $accountCode;

    public $accountToken;

    public $formPageId;

    public $businessName;

    public $backLinkText;

    public $backLinkUrl;

    public $lastFetchTime = 0;

    protected $optionName = 'odrf_settings';

    protected $failedLoad = false;

    private $syncProps = ['backLinkText', 'backLinkUrl'];

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

    public function fetch() : self
    {
        $settingsRequest =
            ReviewFormsClient::instance( $this->accountCode, $this->accountToken )
                ->settings();

        $this->failedLoad = ! $settingsRequest->isHealthy();

        if( $this->failedLoad )

            return $this;

        $data = $settingsRequest->result();

        $this->updateProperties( array_intersect_key( $data, array_flip( $this->syncProps ) ) );

        $this->lastFetchTime = time();

        $this->save();

        return $this;
    }

    public function isOutOfDate() : bool
    {
        return ! $this->lastFetchTime || ($this->lastFetchTime + DAY_IN_SECONDS) < time();
    }

}