<?php

namespace OdReviewForm\OddDog\ClientReviewForm\Views;

use NumberFormatter;

use OdReviewForm\Core\Interfaces\HtmlOutputInterface;
use OdReviewForm\Core\Traits\HtmlOutput;
use OdReviewForm\OddDog\ClientReviewForm\Locations\Locations;
use OdReviewForm\OddDog\ClientReviewForm\Plugin;
use OdReviewForm\OddDog\ClientReviewForm\Reviews\Review;
use OdReviewForm\OddDog\ClientReviewForm\Reviews\Reviews;

use OdReviewForm\OddDog\ClientReviewForm\Views\ReviewAggregate as ReviewAggregateView;
use OdReviewForm\OddDog\ClientReviewForm\Views\Reviews as ReviewsView;

class ReviewsDisplay implements HtmlOutputInterface
{

    use HtmlOutput;

    public static $styleOptions = [ 'default', 'boxed', 'quote' ];

    private $style;

    private $aggregate;

    private $reviews;

    private $pagination;

    private $location;

    /** @var int */
    private $columns;

    public function __construct( array $options ) {

        $this->style = isset($options['style']) ? $options['style'] : static::$styleOptions[0];

        if( ! in_array( $options['style'], self::$styleOptions )  ) return 'Please provide a valid style from the following list: '. implode( ', ', self::$styleOptions );

        $this->aggregate = isset($options['aggregate']) ? (int)$options['aggregate'] : 1;
        $this->reviews = isset($options['reviews']) ? (int)$options['reviews'] : 10;
        $this->pagination = isset($options['pagination']) ? (int)$options['pagination'] : 1;

        $this->location = $options['location'] ?: null;

        $this->columns = !empty($options['columns']) ? (int)$options['columns'] : 1;

    }

    public static function enqueueResources()
    {
        static $queued = false;

        if( $queued )

            return;

        $queued = true;

        wp_enqueue_style( 'odreviews', Plugin::instance()->getUrl( 'css/odreviews.css' ) );
//        wp_enqueue_script( 'odreviewform', Plugin::instance()->getUrl( 'js/odreviewform.js' ) );
    }

    public function getHtml(): string
    {

        if( ! empty( $this->location ) ) {

            if( !Locations::instance()->containsKey( $this->location ) )

                return 'Please provide a valid location from the following list: '. implode( ', ', Locations::instance()->keys() );

            $location = Locations::instance()->get( $this->location );

            if( $location->isDefault() ) $location = null;

        }

        $wrapperClasses = [ "odrf-reviews", "odrf-style-{$this->style}" ];

        $content = '<div class="'. implode(" ", $wrapperClasses) .'">';

        if( $this->aggregate )  $content .= ((new ReviewAggregateView( $this->location )));

        if( $this->reviews ) {

            $reviews = new Reviews();
            $queryConfig = $reviews->newQueryConfig( $this->reviews );

            if( isset($location) && !$location->isDefault() ) {
                $queryConfig['meta_key'] = Review::LOCATION_META_KEY;
                $queryConfig['meta_value'] = $this->location;
            }

            $reviews = (new Reviews())
                ->query( $queryConfig );

            if( $this->isColumned() ) $content .= '<div  class="odrf-cols '. $this->columnClass() .'">';

            $content .= (new ReviewsView( $reviews, $this->style, $this->isColumned() ));

            if( $this->isColumned() ) $content .= '</div>';

            if( $this->pagination ) {

                $query = $reviews->lastQuery();

                $foundPosts = (int)$query->found_posts;
                $page = (int)$query->query_vars['paged'];
                $perPage = (int)$query->query_vars['posts_per_page'];

                $content .= (new ReviewPages( $foundPosts, $page, $perPage ));

            }

        }

        $content .=
            '<p class="odrf-footer">'. (new OddDogLinkBack($this->location)) .'</p>'.
            '</div>';

        return $content;
    }

    private function columnClass() : string
    {
        return 'odrf-'. (new NumberFormatter("en", NumberFormatter::SPELLOUT))->format($this->columns) . '-column';
    }

    private function isColumned() : bool
    {
        return $this->columns > 1;
    }

}