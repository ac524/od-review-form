<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Views\Shortcodes;


use OdReviewForm\Core\Database\Exceptions\InvalidDatabaseConfig;
use OdReviewForm\Core\Plugin\Components\Component;
use OdReviewForm\Core\Plugin\Interfaces\ComponentInterface;
use OdReviewForm\OddDog\ClientReviewForm\Plugin;
use OdReviewForm\OddDog\ClientReviewForm\Reviews\Review;
use OdReviewForm\OddDog\ClientReviewForm\Reviews\ReviewAggregate;
use OdReviewForm\OddDog\ClientReviewForm\Views\Forms\UserReviewForm;
use OdReviewForm\OddDog\ClientReviewForm\Views\ReviewSubmissionThankYou;

class ReviewForm extends Component
{

    protected $id = 'OdClientReviewFormShortcode';

    protected $name = 'OD_REVIEW_FORM';

    /** @var Review */
    protected $review;

    public function registerHooks(): ComponentInterface
    {
        add_shortcode( $this->name, [ $this, 'getContent' ] );

        add_action( 'wp_enqueue_scripts', [ $this, 'enqueueDetection' ] );

        return $this;
    }

    public function enqueueDetection()
    {
        global $post;

        if( $post && has_shortcode( $post->post_content, 'OD_REVIEW_FORM' ) )

            $this->enqueueResources();
    }

    public function enqueueResources()
    {
        static $queued = false;

        if( $queued )

            return;

        $queued = true;

        wp_enqueue_style( 'odreviewform', Plugin::instance()->getUrl( 'css/odreviewform.css' ) );
        wp_enqueue_script( 'odreviewform', Plugin::instance()->getUrl( 'js/odreviewform.js' ) );
    }

    public function getContent( $atts ) : string
    {

        $options = shortcode_atts( [
            'location' => null
        ], $atts );

        $this->enqueueResources();

        $form = new UserReviewForm( $options['location'] );

        if( $form->hasSubmission() && ! $form->hasErrors() )

            try {

                $this->review = (new Review( $form->data() ))->save();

                ReviewAggregate::clearAll();

            } catch( InvalidDatabaseConfig $exception ) {

                $this->review = false;

            }

        $content = $this->review

            ? new ReviewSubmissionThankYou( $this->review )

            : $form;

        return $content;
    }

//    public function email

}