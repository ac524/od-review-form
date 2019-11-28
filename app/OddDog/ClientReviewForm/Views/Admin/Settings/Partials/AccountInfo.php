<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Views\Admin\Settings\Partials;

use OdReviewForm\OddDog\ClientReviewForm\Views\Admin\Settings\SettingsPage;
use OdReviewForm\OddDog\ClientReviewForm\Account\AccountInfo as AccountInfoData;

class AccountInfo extends SettingPageHtmlOutput
{
    /** @var  AccountInfoData */
    private $info;

    private $settingsForm;

    private $formPageUrl;

    public function __construct(SettingsPage $settingsPage)
    {
        parent::__construct($settingsPage);

        $this->info = AccountInfoData::instance();

        $this->settingsForm = $this->settingsPage->component()->getForms()
            ->get( 'GeneralSettings' )
            ->setInputValues( $this->settingsPage->settings() );
    }

    public function getHtml(): string
    {
        return
        '<div id="poststuff" class="postbox">'.
            '<h2>Account Information</h2>'.
            '<div class="inside">'.
                $this->settingsForm .
                '<hr><strong>Form Page:</strong> <a href="'. $this->info->formPageUrl() .'">'. $this->info->formPageUrl() .'</a>'.
                '<br><br><strong>Display Reviews Shortcode:</strong> [OD_REVIEWS]'.
            '</div>'.
        '</div>';
    }
}