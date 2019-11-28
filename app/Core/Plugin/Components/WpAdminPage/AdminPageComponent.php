<?php
/**
 * Created by PhpStorm.
 * User: ac524
 * Date: 2/22/2019
 * Time: 1:10 PM
 */

namespace OdReviewForm\Core\Plugin\Components\WpAdminPage;


use OdReviewForm\Core\Plugin\Components\Component;
use OdReviewForm\Core\Traits\ImplementsInterfaces;
use OdReviewForm\Core\Plugin\Components\Traits\ComponentEnqueues;
use OdReviewForm\Core\Plugin\Components\Traits\ComponentRequestData;
use OdReviewForm\Core\Plugin\Interfaces\ComponentInterface;
use OdReviewForm\Core\Plugin\Interfaces\EnqueueResourcesInterface;
use OdReviewForm\Core\Plugin\Interfaces\RequestDataInterface;

/**
 * Class AdminPageComponent
 * @package ComposerPress\Plugin\Components
 *
 * @method bool hasActiveRequest() - Implementation of RequestDataInterface::isMatchingRequestMethod()
 * @see ComponentRequestData::hasActiveRequest()
 *
 */
abstract class AdminPageComponent extends Component
{

    use ImplementsInterfaces;

    /** @var string */
    protected $pageTitle;

    /** @var string */
    protected $menuTitle;

    /**
     * @var string
     * @see https://codex.wordpress.org/Roles_and_Capabilities
     */
    protected $requiredPermission = 'manage_options';

	/**
	 * Register the admin page to WordPress.
	 */
	public abstract function addPage() : void;

    /**
     * Register the admin page to WordPress.
     */
    public abstract function pageUrl() : string;

	/**
	 * Print the HTML for the admin page.
	 */
	public abstract function printPage() : void;

    /**
     * @return AdminPageComponent
     */
    public function registerHooks(): ComponentInterface
    {

        if( is_admin() ) {

        	// Register the admin page
            add_action( 'admin_menu', [ $this, 'addPage' ] );

            // Exit if we are not currently loading this target page.
            if( ! $this->isLoadingPage() )

                return $this;

            /**
             * Actions specifically for loading this page only below here.
             */

            // Does this admin page have registered data requests for processing?
            if( $this->implements( RequestDataInterface::class ) ) {

	            /**
	             * @see RequestDataInterface::registerRequests()
	             */
            	$this->registerRequests();

            	if( $this->hasActiveRequest() )

            		// Add request processing if one of the registered requests matches the current submission.
	                add_action( 'current_screen', [ $this, 'processRequestData' ] );

            }

            // Does this admin page have custom resources to enqueue?
	        if( $this->implements( EnqueueResourcesInterface::class ) )

                /** @see ComponentEnqueues::enqueueResources() */
                add_action( 'admin_enqueue_scripts', [ $this, 'enqueueResources' ] );

        }

        return $this;

    }

    public function getPageTitle() : string
    {

        return $this->pageTitle ?? $this->getPlugin()->getName();

    }

    public function getMenuTitle() : string
    {

        return $this->pageTitle ?? $this->getPlugin()->getName();

    }

    public function getRequiredPermission() : string
    {

        return $this->requiredPermission;

    }

    protected function isLoadingPage() : bool
    {

        return $this->getSlug() === filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );

    }

}