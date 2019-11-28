<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Views;


use OdReviewForm\Core\Interfaces\HtmlOutputInterface;
use OdReviewForm\Core\Traits\HtmlOutput;
use OdReviewForm\OddDog\ClientReviewForm\Reviews\Review;
use OdReviewForm\OddDog\ClientReviewForm\Reviews\Reviews as ReviewEntries;

class Reviews implements HtmlOutputInterface
{

    use HtmlOutput;

    /**
     * @var Reviews
     */
    private $reviews;

    public function __construct( ReviewEntries $reviews )
    {
        $this->reviews = $reviews;
    }

    public function getHtml(): string
    {
        if( $this->reviews->isEmpty() ) {

            return '<p>There are no reviews yet. <a href="#">Be the first!</a></p>';

        }

        $content = '';

        /** @var Review $review */
        foreach ( $this->reviews->all() as $review ) {

            $content .= sprintf(
                '<div class="odrf-review">'.
                    '<div class="odrf-reviewer">%s</div>'.
                    '%s'.
                    '<p class="odrf-date">%s</p>'.
                    '<p  class="odrf-message">%s</p>'.
                '</div>',
                $review->reviewer,
                new RatingStars( $review->rating ),
                get_the_date( '', $review->post() ),
                $review->reviewMessage
            );

        }

        return $content;
    }

}