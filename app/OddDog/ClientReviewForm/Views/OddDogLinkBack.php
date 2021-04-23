<?php

namespace OdReviewForm\OddDog\ClientReviewForm\Views;

use OdReviewForm\OddDog\ClientReviewForm\Account\AccountInfo;

class OddDogLinkBack extends HtmlOutput
{
    /** @var string */
    private $url;

    /** @var string */
    private $text;

    public function __construct()
    {
        $settings = AccountInfo::instance()->settings;

        $this->url = $settings->backLinkUrl ?: "https://odd.dog/";

        $this->text = $settings->backLinkText ?: "Reviews Powered by Odd Dog Media";
    }

    public function getHtml(): string
    {
        return sprintf('<a href="%s">%s</a>', $this->url, $this->text);
    }

}