<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Views;


use OdReviewForm\Core\Interfaces\HtmlOutputInterface;
use OdReviewForm\Core\Traits\HtmlOutput;
use OdReviewForm\OddDog\ClientReviewForm\Reviews\Review;

class ReviewSubmissionThankYou implements HtmlOutputInterface
{

    use HtmlOutput;

    /** @var Review  */
    private $review;

    public function __construct( Review $review )
    {
        $this->review = $review;
    }

    public function getHtml(): string
    {
        $html = '<h2>Thank You '. $this->review->reviewer .'</h2>';

        $html .= '<p>Your feedback is greatly appreciated!</p>';

        return $html;
    }

}