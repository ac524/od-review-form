<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Rest;


use OdReviewForm\OddDog\ClientReviewForm\Locations\Locations;
use OdReviewForm\OddDog\ClientReviewForm\Rest\Traits\LocationLinksArguments;
use OdReviewForm\OddDog\ClientReviewForm\Settings;
use OdReviewForm\OddDog\FormsApiClient\ReviewFormsClient;
use WP_Error;

class LocationLinksController extends AbstractAdminActionController
{

    use LocationLinksArguments;

    public function register_routes()
    {
        $basePath = '/links';

        $baseItemPath = $basePath . $this->locationIdUrlParam();

        register_rest_route( $this->namespace, $baseItemPath, [
            [
                'methods'  => 'POST',
                'callback' => [ $this, 'create_item' ],
                'permission_callback' => [ $this, 'get_user_auth_check' ],
                'args' => $this->locationLinkArgs( [
                    'resend' => [
                        'type' => 'boolean',
                        'default' => false
                    ]
                ] )
            ]
        ]);
    }

    /**
     * @param \WP_REST_Request $request
     * @return string|\WP_Error|\WP_REST_Response
     */
    public function get_item( $request )
    {
        $location = Locations::instance()->get( $request->get_param( 'locationId' ) );

        if( ! $location->linkId )

            return new \WP_Error( 'location_link', 'The link for this location has not been registered' );

        return 'url for page?';
    }

    /**
     * @param \WP_REST_Request $request
     * @return \WP_Error|array
     */
    public function create_item( $request )
    {
        $location = Locations::instance()->get( $request->get_param( 'locationId' ) );

        if( $location->linkId && ! $request->get_param( 'resend' ) )

            return [
               'location' => $location
            ];

//        var_dump( $location );
//        die();

        $request = ReviewFormsClient
            ::instance( Settings::getInstance()->accountCode, Settings::getInstance()->accountToken )
            ->addLocationLink( 'Website', $location->url(), $location->name );

        if( $request->isHealthy() ) {

            $location->linkId = $request->result()['linkId'];

            Locations::instance()->save();

            return [
                'location' => $location
            ];

        }

        $message = $request->result()['message'] ?? 'Unable to register link';

        return new WP_Error( 'register_link', $message );
        
    }

}