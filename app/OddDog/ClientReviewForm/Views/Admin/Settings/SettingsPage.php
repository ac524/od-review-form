<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Views\Admin\Settings;


use OdReviewForm\Core\Plugin\Components\WpAdminPage\AdminPageComponent;
use OdReviewForm\OddDog\ClientReviewForm\Admin\SettingsPageComponent;
use OdReviewForm\OddDog\ClientReviewForm\Settings;
use OdReviewForm\OddDog\ClientReviewForm\Views\Admin\Settings\Partials\AccountInfo;
use OdReviewForm\OddDog\ClientReviewForm\Views\Admin\Settings\Partials\Locations;
use OdReviewForm\OddDog\ClientReviewForm\Views\Admin\Settings\Partials\NewAccountSetup;
use OdReviewForm\OddDog\ClientReviewForm\Views\Admin\Settings\Partials\Notice;
use OdReviewForm\OddDog\ClientReviewForm\Views\HtmlOutput;

class SettingsPage extends HtmlOutput
{

    /** @var AdminPageComponent|SettingsPageComponent */
    private $component;

    /** @var Settings  */
    private $settings;

    /** @var Notice */
    private $notice;

    /** @var NewAccountSetup|null  */
    private $newSetupView;

    /** @var Locations|null */
    private $locationsView;

    /** @var AccountInfo|null */
    private $accountInfoView;

    public function __construct( AdminPageComponent $component )
    {
        $this->component = $component;

        $this->settings = Settings::getInstance();

        if( $this->settings->hasCode() ) {

            if( ! $this->settings->isCodeValidated() ) {

                if( empty( $component->hasPageStatus() ) )

                    $this->notice = new Notice( 'The account code provided is not valid' );

                $this->configureNewAccountPage();

            } else {

                $this->configureKnownAccountPage();

            }

        } else {

            $this->configureNewAccountPage();

        }
    }

    public function getHtml(): string
    {
        $html =
            '<div class="wrap"><h1>'. $this->component->getPageTitle() .'</h1>'.
                $this->notice.
            '<div id="col-container" class="wp-clearfix">';

        if( $this->newSetupView )

            $html .= $this->newSetupView;

        else {

            $html .=
            '<div id="col-left"><div class="col-wrap">'.

                $this->accountInfoView .
                $this->locationsView .

            '</div></div>';

        }

        $html .= '</div></div>';

        return $html;
    }

    public function component() : SettingsPageComponent
    {
        return $this->component;
    }

    public function settings() : Settings
    {
        return $this->settings;
    }

    private function configureNewAccountPage()
    {
        $this->newSetupView = new NewAccountSetup( $this );
    }

    private function configureKnownAccountPage()
    {
        $this->locationsView = new Locations( $this );

        $this->accountInfoView = new AccountInfo( $this );
    }

}