<?php
/**
 * Created by PhpStorm.
 * User: ac524
 * Date: 2/23/2019
 * Time: 8:35 AM
 */


namespace OdReviewForm\Core\Traits;

/**
 * Trait ImplementsInterfaces
 */
trait ImplementsInterfaces
{

    private $implementedInterfaces;

	/**
	 * @param string $interfaceClassName
	 * @param null $object
	 *
	 * @return bool
	 */
    public function implements( string $interfaceClassName, $object = null ) : bool
    {
    	if( ! empty( $object ) )

    		return isset( class_implements( $object )[ $interfaceClassName ] );

        if( null === $this->implementedInterfaces )

            $this->implementedInterfaces = class_implements( $this );

        return isset( $this->implementedInterfaces[ $interfaceClassName ] );

    }

}