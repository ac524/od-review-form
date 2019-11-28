<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Schema;


use OdReviewForm\OddDog\ClientReviewForm\Account\AccountInfo;
use OdReviewForm\OddDog\ClientReviewForm\Locations\Location;
use OdReviewForm\OddDog\ClientReviewForm\Reviews\ReviewAggregate;

class Schema
{

    /** @var AccountInfo */
    private $accountInfo;

    /** @var ReviewAggregate */
    private $aggregate;

    /** @var Location */
    private $location;

    public function __construct( ?Location $location = null )
    {
        $this->accountInfo = AccountInfo::instance();

        if( ! empty( $location ) )

            $this->setLocation( $location );

        else

            $this->loadAggregate();
    }

    public function setLocation( Location $location ) : self
    {
        $this->location = $location;

        $this->loadAggregate();

        return $this;
    }

    public function data() : array
    {
        global $post;

        $data = [
            "@context" => "http://schema.org/",
            "@type" => "Organization",
            "url" => home_url(),
        ];

        if( ! empty( $this->accountInfo->settings->businessName ) )

            $data["name"] = $this->accountInfo->settings->businessName;

        $data["aggregateRating"] = [
            "@type" => "AggregateRating",
            "url" => get_permalink( $post->ID ),
            "ratingValue" => $this->aggregate->average,
            "reviewCount" => $this->aggregate->count
        ];

        if( $this->hasLocation() ) {

            $address = [];

            foreach ( $this->location->address as $key => $value ) {

                if( ! empty( $value ) ) {

                    $key = $this->addressPartSchemaName( $key );

                    if( ! empty( $key ) )

                        $address[ $key ] = $value;
                }

            }

            if( ! empty( $address ) ) {

                $address['@type'] = 'PostalAddress';

                $data["location"] = [
                    "@type" => "Place",
                    'address' => $address
                ];

                if( ! empty( $this->accountInfo->settings->businessName ) )

                    $data["location"]["name"] = $this->accountInfo->settings->businessName;

            }

        }

        return $data;

    }

    public function JSON() : ?string
    {
        return json_encode( $this->data(), JSON_PRETTY_PRINT ) ?: null;
    }

    public function hasLocation() : bool
    {
        return ! empty( $this->location );
    }

    public function hasUniqueLocation() : bool
    {
        return $this->hasLocation() && ! $this->location->isDefault();
    }

    private function addressPartSchemaName( string $name ) : ?string
    {
        static $map = [
            "name" => "name",
            "streetAddress" => "streetAddress",
            "address2" => "addressLocality",
            "state" => "addressRegion",
            "postal" => "postalCode",
//            "telephone" => "+1800-840-5383",
//            "faxNumber" => "+1707-591-9475"
        ];

        return $map[ $name ] ?? null;
    }

    private function loadAggregate() : void
    {
        $this->aggregate = $this->hasUniqueLocation()

            ? ReviewAggregate::getLocationInstance( $this->location->id() )

            : ReviewAggregate::getInstance();
    }

}