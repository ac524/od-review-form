<?php

namespace OdReviewForm\OddDog\ClientReviewForm\Views;

use OdReviewForm\OddDog\ClientReviewForm\Account\AccountInfo;
use OdReviewForm\OddDog\ClientReviewForm\Locations\Locations;

class OddDogLinkBack extends HtmlOutput
{
    /** @var string */
    private $url;

    /** @var string */
    private $text;

    public function __construct( ?string $locationKey )
    {
        $options = $locationKey && Locations::instance()->containsKey( $locationKey ) ? $this->getLocationOptions( $locationKey ) : [ 'url' => "", 'text' => "" ];

        $settings = AccountInfo::instance()->settings;

        if( !$options['url'] && $settings->backLinkUrl ) $options['url'] = $settings->backLinkUrl;
        if( !$options['text'] && $settings->backLinkText ) $options['text'] = $settings->backLinkText;

        $this->url = $options['url'] ?: "https://odd.dog/";

        $this->text = $options['text'] ?: "Reviews Powered by Odd Dog Media";
    }

    public function getHtml(): string
    {
        return sprintf('<a href="%s">%s</a>', $this->url, $this->text);
    }

    private function getLocationOptions( string $locationKey ) : array
    {
        $location = Locations::instance()->get($locationKey);

        return [
            'url' => $location->backLinkUrl,
            'text' => $location->backLinkText
        ];
    }

}