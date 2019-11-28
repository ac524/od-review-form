<?php
/**
 * Created by PhpStorm.
 * User: ac524
 * Date: 2/21/2019
 * Time: 3:09 PM
 */

namespace OdReviewForm\Core\Plugin\Components;


use OdReviewForm\Core\Plugin\Interfaces\ComponentInterface;

abstract class DynamicRequestComponent extends Component
{

    public function registerHooks(): ComponentInterface
    {

        add_filter('do_parse_request', [$this, 'maybeDoRequest']);

        return $this;

    }

    /**
     * If the request matches the component, serve the custom request and return false. Otherwise, let WP continue as normal.
     *
     * @param bool $parseWpRequest
     * @param \WP $wp
     * @param $extraQueryVars
     *
     * @return bool
     *
     */
    public function maybeDoRequest(bool $parseWpRequest): bool
    {

	    if( $this->isRequestMatch() )

		    $this->serveRequest();

	    return $parseWpRequest;

    }

    abstract function isRequestMatch(): bool;

    abstract function serveRequest(): void;

}