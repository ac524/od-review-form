<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Locations;


use OdReviewForm\Core\Collections\MapClassCollection;
use OdReviewForm\OddDog\ClientReviewForm\ClientConfig;
use OdReviewForm\OddDog\ClientReviewForm\Exceptions\OddDogReviewsException;
use OdReviewForm\OddDog\ClientReviewForm\Reviews\Review;
use OdReviewForm\OddDog\ClientReviewForm\Settings;
use OdReviewForm\OddDog\FormsApiClient\ReviewFormsClient;

/**
 * Class Locations
 * @package ComposerPress\OddDog\ClientReviewForm\Locations
 *
 * @method Location[] all()
 * @see MapClassCollection::all()
 * @method Location get()
 * @see MapClassCollection::get()
 * @method Location first()
 * @see MapClassCollection::first()
 * @method Locations filter()
 * @see MapClassCollection::filter($callback)
 */
class Locations extends MapClassCollection
{

    private static $instance;

    public $failedLoad = false;

    private $isFromCache = false;

    private $lastFetchTime = 0;

    /**
     * @return Locations
     */
    public static function instance() : self
    {
        if( null === self::$instance )

            self::$instance =  self::createInstance();

        return self::$instance;
    }

    /**
     * @return Locations
     */
    private static function createInstance() : self
    {
        $location = new self;

        $location->isFromCache = $location->loadCache();

        if( ! $location->loadCache() )

            $location->fetch();

        return $location;
    }

    /**
     * @return bool
     */
    public function isOutOfDate() : bool
    {
        return ! $this->lastFetchTime || ($this->lastFetchTime + DAY_IN_SECONDS) < time();
    }

    public function lastFetchTime()
    {
        return $this->lastFetchTime;
    }

    public function updated() : Locations
    {
        return $this->filter( function( Location $location ) { return $location->isUpdated(); } );
    }

    /**
     * @return Locations
     * @throws OddDogReviewsException
     */
    public function fetch() : self
    {
        $settings = Settings::getInstance();

        $locationsRequest =
            ReviewFormsClient::instance( $settings->accountCode, $settings->accountToken )
                ->locations();

        $this->failedLoad = ! $locationsRequest->isHealthy();


        if( $this->failedLoad )

            return $this;

        if( empty( $locationsRequest->result()['locations'] ) ) {

            if( ! $this->isEmpty() )

                $this->clear();

            $this->add( 'default', [ 'name' => 'Default' ] );

        } else {

            $currentLocations = $this->keys();
            $fetchedLocations = array_keys( $locationsRequest->result()['locations'] );

            $toAdd = array_diff( $fetchedLocations, $currentLocations );
            $toRemove = array_diff( $currentLocations, $fetchedLocations );
            $toUpdate = array_intersect( $currentLocations, $fetchedLocations );

            if( in_array( 'default', $toRemove ) ) {

                $this->transitionLocation( 'default', $toAdd[0] );

            }

            foreach ( $toAdd as $locationId )

                $this->add( $locationId, $locationsRequest->result()['locations'][ $locationId ] );

            foreach ( $toRemove as $locationId )

                $this->remove( $locationId );

            foreach ( $toUpdate as $locationId )

                $this->update( $locationId, $locationsRequest->result()['locations'][ $locationId ] );
        }

        $this->lastFetchTime = time();

        return $this->save();
    }

    public function transitionLocation( string $fromLocationId, string $toLocationId )
    {
        if( 'default' === $fromLocationId ) {

            global $wpdb;

            $wpdb->update(
                $wpdb->postmeta,
                [
                    'meta_value' => $toLocationId
                ],
                [
                    'meta_key' => Review::LOCATION_META_KEY,
                    'meta_value' => NULL
                ]
            );

        }
    }

    public function update( string $locationId, array $details ) : self
    {
        if( ! $this->containsKey( $locationId ) )

            return $this;

        $this->get( $locationId )->update( $details );

        return $this;
    }

    /**
     * @param string $locationId
     * @param array $details
     * @return Locations
     * @throws OddDogReviewsException
     */
    public function add( string $locationId, array $details ) : self
    {
        if( $this->containsKey( $locationId ) )

            return $this;

        $details['id'] = $locationId;

        $this->set( $locationId, new Location( $details ) );

        return $this;
    }

    /**
     * @return bool
     */
    public function isFromCache() : bool
    {
        return $this->isFromCache;
    }

    /**
     * @return Locations
     */
    public function save() : self
    {
        update_option( ClientConfig::LOCATIONS_OPTION_KEY, json_encode([
            'lastFetchTime' => $this->lastFetchTime,
            'locations' => $this->all()
        ]) );

        return $this;
    }

    /**
     * @return array
     */
    public function names() : array
    {
        return array_map( function( Location $location ) { return $location->name; }, $this->elements );
    }

    /**
     * @return bool
     * @throws OddDogReviewsException
     */
    private function loadCache() : bool
    {
        $locationsData = get_option( ClientConfig::LOCATIONS_OPTION_KEY );

        if( false === $locationsData )

            return false;

        $locationsData = json_decode( $locationsData, true );

        if( ! is_array( $locationsData ) )

            return false;

        $this->lastFetchTime = $locationsData['lastFetchTime'];

        foreach ( $locationsData['locations'] as $locationData )

            $this->set( Location::idFactory( $locationData['name'] ), new Location( $locationData ) );

        return true;
    }

}