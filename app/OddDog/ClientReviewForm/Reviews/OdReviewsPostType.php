<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Reviews;


use OdReviewForm\Core\Plugin\Components\PostTypeComponent;
use OdReviewForm\Core\Plugin\Components\Traits\PostTypeComponentCustomColumns;
use OdReviewForm\Core\Plugin\Interfaces\ComponentInterface;
use OdReviewForm\Core\Plugin\Interfaces\CustomColumnsInterface;
use OdReviewForm\OddDog\ClientReviewForm\Locations\Locations;
use OdReviewForm\OddDog\ClientReviewForm\Settings;
use OdReviewForm\OddDog\ClientReviewForm\Views\RatingStars;

class OdReviewsPostType extends PostTypeComponent implements CustomColumnsInterface
{
    use PostTypeComponentCustomColumns;

    const POST_TYPE = 'odreview';

    const COMPONENT_ID = 'OdReviewsPostType';

    protected $id = self::COMPONENT_ID;

    public $postType = self::POST_TYPE;

    public $label = 'Reviews';

    public $labels = [
        'edit_item' => 'OddDog Review'
    ];

    public $description = 'OddDog reviews';

    public $showUi = true;

//    public $capabilityType = 'odreview';

    public $capabilities = [
        'create_posts' => 'do_not_allow',
        'delete_posts' => 'do_not_allow'
    ];

    public $mapMetaCap = true;

    public $supports = false;

    public $menuIcon = 'dashicons-star-filled';

    public $menuPosition = 26;

    public function registerHooks(): ComponentInterface
    {
        if( ! Settings::getInstance()->isCodeValidated() )

            return $this;

        parent::registerHooks();

        add_action( 'current_screen', [ $this, 'registerScreenHooks' ] );

        return $this;
    }

    public function registerScreenHooks()
    {

        /** @see PostTypeComponentCustomColumns::registerCustomColumns() */
        $this->registerCustomColumns();

        $currentScreen = get_current_screen();

        if( $currentScreen->id === 'edit-'. $this->postType )

            add_action( 'admin_enqueue_scripts', [ $this, 'registerStyles' ] );
    }

    public function registerStyles()
    {
        wp_enqueue_style( 'odrf-admin', $this->getPlugin()->getUrl(  'css/admin.css' ) );
    }

    public function addDashboardColumns()
    {
        $this->dashboardColumns['cb'] = '&lt;input type="checkbox" />';
        $this->dashboardColumns['title'] = __( 'Name' );
        $this->dashboardColumns['ratingstars'] = __( 'Rating' );

        if( Locations::instance()->count() > 1 )

            $this->dashboardColumns['location'] = __( 'Location' );

        $this->dashboardColumns['date'] = __( 'Date' );
    }

    protected function printRatingstarsCustomColumn( $postId )
    {
        echo (new RatingStars( Review::postIdFactory( $postId )->rating ));
    }

    protected function printLocationCustomColumn( $postId )
    {
        echo Review::postIdFactory( $postId )->locationName() ?? 'No Location';
    }

}