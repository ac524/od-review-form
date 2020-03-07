<?php

use OdReviewForm\OddDog\ClientReviewForm\Plugin as OddDogReviewFormPlugin;

$odRFAutoloadFile = __DIR__ . '/vendor/autoload.php';

if( file_exists( $odRFAutoloadFile ) )

    require_once $odRFAutoloadFile;

/**
 * @return OddDogReviewFormPlugin
 */
function get_od_review_form_plugin() {

    return OddDogReviewFormPlugin::setPluginFile( __DIR__ .'/OddDogReviewForm.php' )::instance();

}