<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Rest\Traits;


use OdReviewForm\OddDog\ClientReviewForm\Locations\Locations;
use OdReviewForm\OddDog\ReviewPlatform\Accounts\Account;

trait LocationLinksArguments
{

    public function locationLinkArgs( array $args = [] ) : array
    {
        return $args + [
            'locationId' => $this->locationArgument()
        ];
    }

    public function locationIdUrlParam() : string
    {
        return '/(?P<locationId>[\d\w]+)';
    }

    protected function locationArgument() : array
    {
        return [
            'required' => true,
            'type' => 'string',
            'validate_callback' => function($param, $request, $key) {

                if ( ! Locations::instance()->containsKey( $param ) )

                    return new \WP_Error('rest_invalid_param', 'Location does not exist');

                return true;
            }
        ];
    }

}