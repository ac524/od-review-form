<?php
/**
 * Created by PhpStorm.
 * User: anthonybrown
 * Date: 2019-03-06
 * Time: 21:45
 */

namespace OdReviewForm\Core\Database\Tables\Rows\Traits;


use OdReviewForm\Core\Collections\MapCollection;
use OdReviewForm\Core\Database\Exceptions\InvalidDatabaseConfig;
use OdReviewForm\Core\Database\Tables\Rows\Rows;

/**
 * Trait RowsPersistentQueryCache
 * @package ComposerPress\Core\Database\Tables\Rows\Traits
 *
 * @method Rows newCollection( bool $includeElements = false )
 * @see MapCollection::newCollection()
 *
 */
trait RowsPersistentQueryCache
{

    private $instanceName;

    private static $cache = [];

    /**
     * @return Rows|null
     * @throws InvalidDatabaseConfig
     */
    protected function getQueryCache() : ?Rows
    {

        if( !$this->hasCacheInstance() )

            $this->createCacheInstance();

        return $this->getCacheInstance();

    }

    private function getCacheInstanceName() : string
    {

        if( null === $this->instanceName )

            try {

                $this->instanceName = (new \ReflectionClass($this))->getName();

            } catch( \ReflectionException $exception ) {

                $this->instanceName = '';

            }

        return $this->instanceName;

    }

    private function hasCacheInstance() : bool
    {

        return isset( self::$cache[ $this->getCacheInstanceName() ] );

    }

    /**
     * @throws InvalidDatabaseConfig
     */
    private function createCacheInstance() : void
    {

        if( empty( $this->getCacheInstanceName() ) )

            throw new InvalidDatabaseConfig( 'Cannot create an empty instance name for persistent Row Caching.' );

        self::$cache[ $this->getCacheInstanceName() ] = $this->newCollection();

    }

    private function getCacheInstance() : Rows
    {

        return self::$cache[ $this->getCacheInstanceName() ] ;

    }

}