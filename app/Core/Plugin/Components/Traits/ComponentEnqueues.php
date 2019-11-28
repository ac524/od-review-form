<?php
/**
 * Created by PhpStorm.
 * User: ac524
 * Date: 2/21/2019
 * Time: 3:30 PM
 */

namespace OdReviewForm\Core\Plugin\Components\Traits;

use OdReviewForm\Core\Plugin\Interfaces\EnqueueResourcesInterface;
use OdReviewForm\Core\Plugin\Plugin;

/**
 * Class ComponentResources
 * @package ComposerPress\Plugin\Components\Traits
 *
 * @method Plugin getPlugin()
 *
 * @property array $styles List of WP style enqueue handles or wp_enqueue_style() function parameters
 * @property array $scripts List of WP script enqueue handles or wp_enqueue_script() function parameters
 * @property array $localizedScripts
 *
 * @see \wp_enqueue_style()
 * @see \wp_enqueue_script();
 *
 */
trait ComponentEnqueues
{

    /**
     * @return array|null
     */
    protected function getStyles() : array
    {

        return $this->styles ?? [];

    }

    /**
     * @return array|null
     */
    protected function getScripts() : array
    {

        return $this->scripts ?? [];

    }

	/**
	 * @return array|null
	 */
	protected function getLocalizedScriptsVars() : array
	{

		return $this->localizedScripts ?? [];

	}

    /**
     * @return ComponentEnqueues|EnqueueResourcesInterface
     * @see EnqueueResourcesInterface::enqueueResources()
     */
    public function enqueueResources() : EnqueueResourcesInterface
    {

        return $this
            ->enqueueStyles()
            ->enqueueScripts()
	        ->addLocalizedScripts();

    }

    /**
     * @return ComponentEnqueues
     */
    protected function enqueueStyles() : self
    {

        foreach( $this->getStyles() as $style )

            $this->enqueueResource( 'wp_enqueue_style', $style );

        return $this;

    }

    /**
     * @return ComponentEnqueues
     */
    protected function enqueueScripts() : self
    {

        foreach ( $this->getScripts() as $script )

            $this->enqueueResource( 'wp_enqueue_script', $script );

        return $this;

    }

    protected function addLocalizedScripts() : self
    {

    	foreach ( $this->getLocalizedScriptsVars() as $configuration )

    		call_user_func_array( 'wp_localize_script', $configuration );

    	return $this;

    }

    /**
     * @param string $filename
     * @return string
     */
    protected function getCssFileUrl( string $filename ) : string
    {

        return $this->getPlugin()->getUrl( 'css/'. $filename );

    }

    /**
     * @param string $filename
     * @return string
     */
    protected function getJsFileUrl( string $filename ) : string
    {

        return $this->getPlugin()->getUrl( 'js/'. $filename );

    }

    /**
     * @param $enqueueHandler
     * @param $enqueueResource
     */
    private function enqueueResource( $enqueueHandler, $enqueueResource ) : void
    {

        if( is_string( $enqueueResource ) )

            $enqueueHandler( $enqueueResource );

        else

            call_user_func_array( $enqueueHandler, $enqueueResource );

    }

}