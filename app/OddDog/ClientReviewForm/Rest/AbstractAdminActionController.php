<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Rest;

use OdReviewForm\OddDog\ClientReviewForm\Plugin;
use WP_REST_Controller;

abstract class AbstractAdminActionController extends WP_REST_Controller
{

    public function __construct()
    {
        $this->namespace = 'odrfadmin/v1';
    }

    /**
     * Check permissions for the posts.
     *
     * @param $request
     *
     * @return bool|\WP_Error
     */
    public function get_user_auth_check( $request )
    {
        $settingsPageComponent = Plugin::instance()->getComponent( 'OdClientReviewFormSettings' )->getRequiredPermission();

        if ( ! current_user_can( $settingsPageComponent ) )

            return new \WP_Error( 'rest_forbidden', esc_html__( 'You cannot import resources.' ), array( 'status' => 419 ) );

        return true;
    }

}