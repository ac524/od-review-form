<?php
/**
 * Created by PhpStorm.
 * User: ac524
 * Date: 2/21/2019
 * Time: 1:16 PM
 */

namespace OdReviewForm\Core\Plugin\Components;


use OdReviewForm\Core\Plugin\Interfaces\ComponentInterface;
use OdReviewForm\Core\Plugin\Plugin;

class RestRouteComponent extends Component
{

	/** @var array|null */
	protected $controllers;

	public function registerHooks(): ComponentInterface
	{
		add_action( 'rest_api_init', [ $this, 'registerRestControllers' ] );

		return $this;
	}

	public function registerRestControllers() : void
	{
		if( empty( $this->controllers ) )

			return;

		foreach ( $this->controllers as $controllerClass ) {

			/** @var \WP_REST_Controller $controller */
			$controller = new $controllerClass();

			$controller->register_routes();

		}
	}

}