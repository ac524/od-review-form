<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Views;


use OdReviewForm\Core\Interfaces\HtmlOutputInterface;
use OdReviewForm\Core\Traits\HtmlOutput;

class RatingStars implements HtmlOutputInterface
{

    use HtmlOutput;

    private $rating;

    public function __construct( $rating )
    {
        $this->rating = $rating;
    }

    public function getHtml(): string
    {
        return sprintf(
            '<span class="odrf-stars-wrap"><span class="odrf-stars"%s>%s</span></span>',
            $this->starsMaskStyles(),
            implode( '', array_fill(0, ceil($this->rating), $this->getSvgHtml()) )
        );
    }

    public function starsMaskStyles()
    {
        $ceil = ceil($this->rating);

        if( $this->rating !== $ceil ) {

            $percentage = (1 - ($this->rating/$ceil))*100;

            return ' style="margin-right:'. $percentage .'%"';

        }


        return '';
    }

    private function getSvgHtml()
    {
        return '<span class="odrf-star"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="odrf-star-filled"><path d="M12 .587l3.668 7.568 8.332 1.151-6.064 5.828 1.48 8.279-7.416-3.967-7.417 3.967 1.481-8.279-6.064-5.828 8.332-1.151z"/></svg></span>';
    }

}