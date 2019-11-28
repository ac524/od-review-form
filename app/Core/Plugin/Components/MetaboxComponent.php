<?php


namespace OdReviewForm\Core\Plugin\Components;

use OdReviewForm\Core\Plugin\Interfaces\ComponentInterface;
use OdReviewForm\Core\Plugin\Interfaces\EnqueueResourcesInterface;
use OdReviewForm\Core\Plugin\Interfaces\RequestDataInterface;
use OdReviewForm\Core\Traits\ImplementsInterfaces;

/**
 * Class MetaboxComponent
 * @package ComposerPress\Core\Plugin\Components
 */
abstract class MetaboxComponent extends Component
{

    use ImplementsInterfaces;

    protected $metaboxId;

    protected $title;

    protected $screen;

    protected $context = 'advanced';

    protected $priority = 'high';

    public function registerHooks() : ComponentInterface
    {
        if( ! is_admin() )

            return $this;

        add_action( 'add_meta_boxes', [ $this, 'addMetaBox' ] );

        add_action( 'current_screen', [ $this, 'registerScreenHooks' ] );

        return $this;
    }

    public function isScreen()
    {
        $currentScreen = get_current_screen();

        if( null === $currentScreen )

            _doing_it_wrong( __FUNCTION__, 'Screens cannot be checked before the current screen has been set by wordpress', $this->plugin->getVersion() );

        if( is_string( $this->screen ) )

            return $this->screen === $currentScreen->id;

        if( is_array( $this->screen ) )

            return in_array( $currentScreen->id, $this->screen );

        return false;
    }

    public function addMetaBox()
    {
        add_meta_box( $this->id, $this->title, [ $this, 'printContent' ], $this->screen, $this->context, $this->priority );
    }

    public function registerScreenHooks()
    {

        if( ! $this->isScreen() )

            return;

        if( $this->implements( EnqueueResourcesInterface::class ) )

            /** @see ComponentEnqueues::enqueueResources() */
            add_action( 'admin_enqueue_scripts', [ $this, 'enqueueResources' ] );

        if( $this->implements( RequestDataInterface::class ) ) {

            /** @see RequestDataInterface::registerRequests() */
            $this->registerRequests();

            /** @see RequestDataInterface::hasActiveRequest() */
            if( $this->hasActiveRequest() )

                /** @see RequestDataInterface::processRequestData() */
                add_action( 'save_post', [ $this, 'processRequestData' ] );

        }

    }

    abstract function printContent() : void;

}