<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Views\Shortcodes;


use OdReviewForm\Core\Plugin\Components\Component;
use OdReviewForm\Core\Plugin\Interfaces\ComponentInterface;
use OdReviewForm\OddDog\ClientReviewForm\Locations\Locations;
use OdReviewForm\OddDog\ClientReviewForm\Plugin;
use OdReviewForm\OddDog\ClientReviewForm\Reviews\Review;
use OdReviewForm\OddDog\ClientReviewForm\Schema\Schema;
use OdReviewForm\OddDog\ClientReviewForm\Views\OddDogLinkBack;
use OdReviewForm\OddDog\ClientReviewForm\Views\ReviewAggregate as ReviewAggregateView;
use OdReviewForm\OddDog\ClientReviewForm\Reviews\Reviews;
use OdReviewForm\OddDog\ClientReviewForm\Views\ReviewPages;
use OdReviewForm\OddDog\ClientReviewForm\Views\Reviews as ReviewsView;

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

            $this->enqueueResources();
    }

    public function enqueueResources()
    {
        static $queued = false;

        if( $queued )

            return;

        $queued = true;

        wp_enqueue_style( 'odreviews', Plugin::instance()->getUrl( 'css/odreviews.css' ) );
//        wp_enqueue_script( 'odreviewform', Plugin::instance()->getUrl( 'js/odreviewform.js' ) );
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
            'location' => null
        ], $atts );

//        var_dump( (new Schema())->data() );

//        $this->enqueueResources();

        $content = '';

        $page = max(filter_input( INPUT_GET, 'odrfPage', FILTER_VALIDATE_INT ) ?: 1, 1 );
        $perPage = 10;
        $offset = $perPage * ( $page - 1 );

//        ReviewAggregate::getInstance();

        $reviews = new Reviews();
        $queryConfig = $reviews->newQueryConfig();

        if( ! empty( $options['location'] ) ) {

            if( !Locations::instance()->containsKey( $options['location'] ) )

                return 'Please provide a valid location from the following list: '. implode( ', ', Locations::instance()->keys() );

            $location = Locations::instance()->get( $options['location'] );

            if( $location->isDefault() )

                $options['location'] = null;

            else {
                $queryConfig['meta_key'] = Review::LOCATION_META_KEY;
                $queryConfig['meta_value'] = $options['location'];
            }

        }



        $reviews = (new Reviews())
            ->query( $queryConfig );

        $query = $reviews->lastQuery();

        $foundPosts = (int)$query->found_posts;
        $page = (int)$query->query_vars['paged'];
        $perPage = (int)$query->query_vars['posts_per_page'];

//        var_dump( $query );
//        var_dump( $query->query_vars['paged'] );

        return
            '<div class="odrf-reviews">'.
                (new ReviewAggregateView( $options['location'] )).
                (new ReviewsView( $reviews )).
                (new ReviewPages( $foundPosts, $page, $perPage )).
                '<p class="odrf-footer">'. (new OddDogLinkBack()) .'</p>'.
            '</div>';
    }

}