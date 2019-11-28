<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Views\Admin\Settings\Partials;


use OdReviewForm\OddDog\ClientReviewForm\Views\HtmlOutput;

class Notice extends HtmlOutput
{

    private $status;

    private $message;

    public function __construct( string $message, string $status = 'error' )
    {
        $this->message = $message;

        $this->status = $status;
    }


    public function getHtml(): string
    {
        return sprintf(
        '<div class="notice notice-%s is-dismissible">'.
                '<p>%s</p>'.
            '</div>',
            $this->status,
            $this->message
        );
    }
}