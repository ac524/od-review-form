<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Schema;


use OdReviewForm\Core\Plugin\Components\Component;
use OdReviewForm\Core\Plugin\Interfaces\ComponentInterface;
use OdReviewForm\OddDog\ClientReviewForm\Locations\Location;
use OdReviewForm\OddDog\ClientReviewForm\Locations\Locations;
use OdReviewForm\OddDog\ClientReviewForm\Views\Shortcodes\DisplayReviews;

class PrintSchemaComponent extends Component
{
    protected $id = 'odPrintReviewSchema';

    public function registerHooks(): ComponentInterface
    {
        add_action( 'wp_head', [ $this, 'maybePrintSchema' ] );

        return $this;
    }

    public function maybePrintSchema() : void
    {

        if( ! is_singular() || ! $this->shortcodeComponent()->currentPostHasShortcode() )

            return;

        global $post;

        $pattern = $this->shortcodeComponent()->shortcodeRegex();

        preg_match( "/$pattern/", $post->post_content, $match );

        $options = shortcode_atts( [
            'location' => null
        ], shortcode_parse_atts( $match[3] ) );

        $schema = new Schema();

        if( Locations::instance()->containsKey( $options['location'] ) )

            $schema->setLocation( Locations::instance()->get( $options['location'] ) );

        echo '<script type="application/ld+json">'. $schema->JSON() .'</script>';

    }

    /**
     * @return ComponentInterface|DisplayReviews
     */
    protected function shortcodeComponent()
    {
        return $this->plugin->getComponent( 'OdClientReviewsShortcode' );
    }
}