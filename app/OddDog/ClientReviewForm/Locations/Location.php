<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Locations;

use OdReviewForm\Core\Collections\CollectionClass;
use OdReviewForm\OddDog\ClientReviewForm\Exceptions\OddDogReviewsException;
use OdReviewForm\OddDog\ClientReviewForm\FormPage\FormPage;

class Location extends CollectionClass
{
    protected $id;

    public $name;

    public $linkId;

    public $address;

    private $isUpdated = false;

    public static function idFactory( string $name )
    {
        return preg_replace( '/[^\w]/', '', strtolower( $name ));
    }

    public function __construct( ?array $options = null )
    {
        if( ! empty( $options ) ) {

            if( empty( $options['name'] ) )

                throw new OddDogReviewsException( 'Locations require a name' );

            $this->updateProperties( $options );
        }
    }

    public function id() : string
    {
        if( empty( $this->id ) )

            $this->id = self::idFactory( $this->name );

        return $this->id;
    }

    public function url() : ?string
    {
        $formPageUrl = FormPage::instance()->pageUrl();

        if( $this->isDefault() )

            return $formPageUrl;

        return add_query_arg( 'location', $this->id(), $formPageUrl );
    }

    public function status() : string
    {
        return empty( $this->linkId ) ? 'Unregistered' : 'Registered';
    }

    public function isRegistered() : bool
    {
        return $this->status() === 'Registered';
    }

    public function isDefault()
    {
        return $this->id() === 'default';
    }

    public function isUpdated() : bool
    {
        return $this->isUpdated;
    }

    public function update( array $options ) : self
    {
        array_walk( $options, [ $this, 'updateOption' ] );

        return $this;
    }

    /**
     * Granular update parsing for update tracking.
     * @param $value
     * @param $name
     */
    private function updateOption( $value, $name ) : void
    {
        if( ! property_exists( $this, $name ) )

            return;

        if( is_array( $value ) ) {

            if( ! is_array( $this->{ $name } ) ) {

                $this->{ $name } = $value;
                $this->flagUpdated();
                return;

            }

            foreach ( $value as $key => $subValue  )

                if( !isset( $this->{ $name }[ $key ] ) ) {

                    $this->{ $name }[ $key ] = $subValue;

                    if( ! empty( $this->{ $name }[ $key ] ) )

                        $this->flagUpdated();

                } elseif( $this->{ $name }[ $key ] != $subValue ) {

                    $this->{ $name }[ $key ] = $subValue;
                    $this->flagUpdated();

                }

            return;

        }

        if( $value != $this->{ $name } ) {

            $this->{ $name } = $value;
            $this->flagUpdated();

        }
    }

    private function flagUpdated() : void
    {
        if( ! $this->isUpdated )

            $this->isUpdated = true;
    }

}