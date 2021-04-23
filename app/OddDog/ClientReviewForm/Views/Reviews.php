<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Views;


use OdReviewForm\Core\Interfaces\HtmlOutputInterface;
use OdReviewForm\Core\Traits\HtmlOutput;
use OdReviewForm\OddDog\ClientReviewForm\Locations\Locations;
use OdReviewForm\OddDog\ClientReviewForm\Reviews\Review;
use OdReviewForm\OddDog\ClientReviewForm\Reviews\Reviews as ReviewEntries;

use OdReviewForm\OddDog\ClientReviewForm\Account\AccountInfo;

class Reviews implements HtmlOutputInterface
{

    use HtmlOutput;

    /**
     * @var Reviews
     */
    private $reviews;

    /** @var string */
    private $layout;

    /** @var boolean */
    private $isColumned;

    /** @var Locations */
    private $locations;

    private static $htmlLayoutMap = [
        'default' => 'LayoutOne',
        'boxed' => 'LayoutOne',
        'quote' => 'LayoutTwo'
    ];

    public function __construct( ReviewEntries $reviews, string $layout = "default", bool $isColumned = false )
    {
        $this->reviews = $reviews;

        $this->layout = isset(self::$htmlLayoutMap[$layout]) ? self::$htmlLayoutMap[$layout] : self::$htmlLayoutMap["default"];

        $this->locations = Locations::instance();

        $this->isColumned = $isColumned;
    }

    public function getHtml(): string
    {
        if( $this->reviews->isEmpty() ) {

            return '<p>There are no reviews yet. <a href="'. AccountInfo::instance()->formPageUrl() .'">Be the first!</a></p>';

        }

        return call_user_func( [$this, "getHtml{$this->layout}"] );
    }

    public function getHtmlLayoutOne(): string
    {
        $content = '';

        /** @var Review $review */
        foreach ( $this->reviews->all() as $review ) {

            if( $this->isColumned ) $content .= '<div class="odrf-col">';

            $content .= sprintf(
                '<div class="odrf-review">'.
                '<div class="odrf-reviewer">%s</div>'.
                    '<p class="odrf-date">%s%s</p>'.
                    '<p  class="odrf-message">%s %s</p>'.
                '</div>',
                $review->reviewer,
                human_time_diff(strtotime( $review->post()->post_date )) . ' ago',
                $review->hasLocation() ? ", ". $review->locationName() : "",
                new RatingStars( $review->rating ),
                $review->reviewMessage
            );

            if( $this->isColumned ) $content .= '</div>';

        }

        return $content;
    }

    public function getHtmlLayoutTwo(): string
    {
        $content = '';

        /** @var Review $review */
        foreach ( $this->reviews->all() as $review ) {

            if( $this->isColumned ) $content .= '<div class="odrf-col">';

            $content .= sprintf(
                '<div class="odrf-review">'.
                '<p  class="odrf-message">%s %s</p>'.
                '<div class="odrf-meta">'.
                    '<div class="odrf-reviewer">%s</div>'.
                    '<p class="odrf-date">%s%s</p>'.
                '</div>'.
                '</div>',
                new RatingStars( $review->rating ),
                $review->reviewMessage,
                $review->reviewer,
                human_time_diff(strtotime( $review->post()->post_date )) . ' ago',
                $review->hasLocation() ? ", ". $review->locationName() : "",
            );

            if( $this->isColumned ) $content .= '</div>';

        }

        return $content;
    }

}