<?php
/**
 * Created by PhpStorm.
 * User: anthonybrown
 * Date: 2019-03-05
 * Time: 13:35
 */

namespace OdReviewForm\Core\Collections;

use OdReviewForm\Core\Exceptions\InvalidCollectionConfiguation;
use ReflectionClass;
use ReflectionException;

/**
 * Class MapClassCollection
 * @package ComposerPress\Core\Collections
 *
 * @method CollectionClass get($key)
 * @see MapCollection::get()
 * @method CollectionClass first()
 * @see MapCollection::first()
 * @method CollectionClass last()
 * @see MapCollection::last()
 * @method CollectionClass[] all()
 * @see MapCollection::all()
 * @method MapClassCollection filter($callable)
 * @see MapCollection::filter()
 * @method MapClassCollection filterNot($callable)
 * @see MapCollection::filterNot()
 *
 */
class MapClassCollection extends MapCollection
{

    /** @var CollectionClass[] */
    protected $elements;

    /** @var string */
    protected $collectionClass;

    /**
     * Create a new instance of the collection's class
     *
     * @param mixed ...$classArgs
     * @return mixed
     * @throws InvalidCollectionConfiguation
     */
    public function itemFactory( ...$classArgs ) : CollectionClass
    {

        $collectionClass = $this->getCollectionClass();

        return new $collectionClass( ...$classArgs );

    }

    /**
     * @param string $property
     * @param $value
     * @return CollectionClass
     */
    public function findBy( string $property, $value ) : ?CollectionClass
    {

        foreach (  $this->elements as $element )

            if( $element->getPropertyValue( $property ) === $value )

                return $element;

        return null;

    }

    /**
     * @param string $property
     * @param $value
     * @return MapClassCollection
     */
    public function filterBy( string $property, $value ) : MapClassCollection
    {

    }

	public function isCollectionClass( $object ) : bool
	{
		try {

			return (new ReflectionClass($object))->getName() === $this->getCollectionClass();

		} catch( ReflectionException $e ) {

			// Exit
			die( $e->getMessage() );

		}
    }

    /**
     * @return string
     */
    public function getCollectionClass() : string
    {

        if( empty( $this->collectionClass ) )

        	try {

		        $this->setCollectionClass();

	        } catch( InvalidCollectionConfiguation $e ) {

        	    // Exit
		        die( $e->getMessage() );

	        }

        return $this->collectionClass;

    }

	/**
	 * @param string|null $className
	 *
	 * @return MapCollection
	 * @throws InvalidCollectionConfiguation
	 */
    public function setCollectionClass( ?string $className = null ) : MapCollection
    {

    	if( !empty( $className ) )

    		$this->collectionClass = $className;

        if( empty( $this->collectionClass ) )

            $this->collectionClass = rtrim( static::class, 's' );

        if( !class_exists( $this->collectionClass ) ) {

            $this->collectionClass = null;

            throw new InvalidCollectionConfiguation( static::class .' collection type class does not exist. ' );

        }

        return $this;

    }

}