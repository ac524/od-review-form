<?php

namespace OdReviewForm\OddDog\ClientReviewForm\Views\Blocks\ReviewForm;

use OdReviewForm\Core\Plugin\Components\Component;
use OdReviewForm\Core\Plugin\Interfaces\ComponentInterface;
use OdReviewForm\OddDog\ClientReviewForm\Locations\Locations as LocationsData;

/**
 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/writing-your-first-block-type/
 */
class ReviewFormWpBlockComponent extends Component {

	protected $id = "OdReviewFormWpBlock";

	public function registerHooks(): ComponentInterface
	{
		add_action( 'init', [$this,'registerBlockType'] );

		add_action( 'admin_enqueue_scripts', [$this,'addLocationsData'], 11, 1 );

		return $this;
	}

	public function addLocationsData()
	{
		if( wp_script_is("odrf-reviewform-editor-script") ) {

			$blockConfig = [
				"locations" => LocationsData::instance()->all()
			];

			wp_add_inline_script("odrf-reviews-editor-script", "window.odrfReviewFormBlockConfig = ". json_encode($blockConfig)) .";";

		}
	}

	public function registerBlockType()
	{

		register_block_type_from_metadata( __DIR__ );

	}

}
