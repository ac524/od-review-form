<?php

namespace OdReviewForm\OddDog\ClientReviewForm\Views\Shortcodes;

use OdReviewForm\Core\Plugin\Components\Component;
use OdReviewForm\Core\Plugin\Interfaces\ComponentInterface;
use OdReviewForm\OddDog\ClientReviewForm\Views\ReviewsDisplay;

class DisplayReviews extends Component
{

    protected $id = 'OdClientReviewsShortcode';

    protected $name = 'OD_REVIEWS';

    public function registerHooks(): ComponentInterface
    {
        add_shortcode( $this->name, [ $this, 'getContent' ] );

        add_action( 'wp_enqueue_scripts', [ $this, 'enqueueDetection' ] );

        return $this;
    }

    public function enqueueDetection()
    {
        if( $this->currentPostHasShortcode() )

            ReviewsDisplay::enqueueResources();
    }

    public function shortcodeRegex()
    {
        return get_shortcode_regex( [ $this->name ] );
    }

    public function currentPostHasShortcode()
    {
        global $post;

        return $post && has_shortcode( $post->post_content, $this->name );
    }

    public function getContent( $atts ) : string
    {

        $options = shortcode_atts( [
            'style' => 'default',
            'location' => null,
            'aggregate' => 1,
            'reviews' => 10,
            'pagination' => 1,
            'columns' => 1
        ], $atts );

        return new ReviewsDisplay( $options );

    }

}