<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Views\Admin\Settings\Partials;


use OdReviewForm\OddDog\ClientReviewForm\Views\Admin\Settings\SettingsPage;
use OdReviewForm\OddDog\ClientReviewForm\Views\HtmlOutput;

abstract class SettingPageHtmlOutput extends HtmlOutput
{

    /** @var SettingsPage */
    protected $settingsPage;

    public function __construct( SettingsPage $settingsPage )
    {
        $this->settingsPage = $settingsPage;
    }
}