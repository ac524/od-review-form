<?php

namespace OdReviewForm\OddDog\ClientReviewForm;

use OdReviewForm\OddDog\ClientReviewForm\Admin\ReviewMetaboxComponent;
use OdReviewForm\OddDog\ClientReviewForm\Admin\SettingsPageComponent;
use OdReviewForm\OddDog\ClientReviewForm\Rest\AdminActionsRoutesComponent;
use OdReviewForm\OddDog\ClientReviewForm\Reviews\OdReviewsPostType;
use OdReviewForm\OddDog\ClientReviewForm\Reviews\ReviewAggregatePurgeComponent;
use OdReviewForm\OddDog\ClientReviewForm\Schema\PrintSchemaComponent;
use OdReviewForm\OddDog\ClientReviewForm\Views\Shortcodes\DisplayReviews;
use OdReviewForm\OddDog\ClientReviewForm\Views\Shortcodes\ReviewForm;

class Plugin extends \OdReviewForm\Core\Plugin\Plugin {

	private static $pluginFile;

	private static $instance;

	protected $componentClasses = [
		SettingsPageComponent::class,
        ReviewMetaboxComponent::class,
        ReviewForm::class,
        DisplayReviews::class,
        OdReviewsPostType::class,
        ReviewAggregatePurgeComponent::class,
        AdminActionsRoutesComponent::class,
        PrintSchemaComponent::class
	];

	/**
	 * @param string $pluginFile
	 *
	 * @return Plugin|string
	 */
	public static function setPluginFile( string $pluginFile ) : string
	{
		self::$pluginFile = $pluginFile;

		return __CLASS__;
	}

	/**
	 * @return Plugin|null
	 */
	public static function instance() : ?Plugin
	{
		if( ! empty( self::$instance ) )

			return self::$instance;

		if( empty( self::$pluginFile ) )

			return null;

		try {

			self::$instance = new self( self::$pluginFile );

		} catch ( \Exception $exception ) {

			die( $exception->getMessage() );

		}

		return self::$instance;
	}

}