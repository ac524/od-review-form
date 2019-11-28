<?php

/**
 * Plugin Name: OddDog Review Form
 * Description: Gather reviews on your website. Automatically syncs with your OddDog review request form.
 * Version: 1.11
 * Author: Odd Dog Media
 * Author URI: https://www.odddogmedia.com/
 * Requires PHP: 7.1
 * License: GPLv2 or later
 * ComposerPress Slug: odreviewform
 */

use OdReviewForm\OddDog\ClientReviewForm\Plugin as OddDogReviewFormPlugin;

if ( version_compare(PHP_VERSION, '7.1.0', '>=') ) {

    $odRFAutoloadFile = __DIR__ . '/vendor/autoload.php';

    if( file_exists( $odRFAutoloadFile ) )

        require_once $odRFAutoloadFile;

    /**
     * @return OddDogReviewFormPlugin
     */
    function get_od_review_form_plugin() {

        return OddDogReviewFormPlugin::setPluginFile( __FILE__ )::instance();

    }

    // Fetch the plugin instance to instantiate the class and register all associated components.
    get_od_review_form_plugin();

} else {

    function od_php_version_error_message() {

        $class = 'notice notice-error';
        $message = __( 'The <strong>OddDog Review Form Plugin</strong> requires PHP 7.1 or higher. Please contact <a href="https://www.odddogmedia.com/contact/">Odd Dog Media</a> for help with upgrading your website.', 'text-domain' );

        printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );

    }

    add_action( 'admin_notices', 'od_php_version_error_message' );

}