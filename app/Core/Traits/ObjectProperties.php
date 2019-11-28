<?php
/**
 * Created by PhpStorm.
 * User: ac524
 * Date: 2/23/2019
 * Time: 8:35 AM
 */


namespace OdReviewForm\Core\Traits;

/**
 * Trait ObjectProperties
 *
 * @method preUpdatePropertiesFilter( array $properties )
 *
 */
trait ObjectProperties
{

    /**
     * @param array $properties
     * @return ObjectProperties
     */
    public function updateProperties( array $properties ) : self
    {

	    if( is_callable( [ $this, 'preUpdatePropertiesFilter' ] ) )

	    	$properties = $this->preUpdatePropertiesFilter( $properties );

        foreach ( $this->getWritableProperties() as $propertyKey => $setter ) {

            if( is_string( $propertyKey ) )

                $property = $propertyKey;

            else {

                $property = $setter;

                $setterFn = 'set'. ucfirst( $setter);

                if( is_callable( [ $this, $setterFn ] ) )

                    $setter = $setterFn;

            }
            
            if( isset( $properties[ $property ] ) ) {

	            $propertyValue = $properties[ $property ] === 'null' ? null :  $properties[ $property ];

                $setter !== $property && is_callable( [ $this, $setter ] )

		            ? $this->{ $setter }( $propertyValue )

		            : $this->{ $property } = $propertyValue;

            }


        }

        return $this;

    }

    public function getProperties() : array
    {

        try {

            $reflection = new \ReflectionClass($this);

        } catch( \Exception $exception ) {

            die( $exception->getMessage() );

        }

        $allProperties = $reflection->getProperties();
        $static = $reflection->getProperties(\ReflectionProperty::IS_STATIC );

        return $this->getPropertyNames( array_diff( $allProperties, $static ) );

    }

    /**
     * Return a list of non static properties.
     * @return array
     */
    public function getPublicProperties() : array
    {

        try {

            $reflection = new \ReflectionClass($this);

        } catch( \Exception $exception ) {

            die( $exception->getMessage() );

        }

        $public = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC );
        $static = $reflection->getProperties(\ReflectionProperty::IS_STATIC );

        return $this->getPropertyNames( array_diff( $public, $static ) );

    }

    public function getWritableProperties()
    {

        // TODO Add magic property modifier.

        return $this->getProperties();

    }

    /**
     * Return return the value of scope accessible variable
     *
     * TODO Add getter methods detection for magic private variable access.
     *
     * @param string $property
     * @return mixed
     */
    public function getPropertyValue( string $property )
    {

        return $this->{ $property } ?? null;

    }


    /**
     * Return object property values as a list
     *
     * @param array|null $properties
     * @return array
     */
    public function getPropertyValues( ?array $properties = null ) : array
    {

        if( empty( $properties ) )

            $properties = $this->getProperties();

        $values = [];

        foreach ( $properties as $property )

            $values[ $property ] = $this->getPropertyValue( $property );

        return $values;

    }

    /**
     * @param \ReflectionProperty[] $properties
     * @return array
     */
    private function getPropertyNames( array $properties )
    {

        return array_map(

            function( \ReflectionProperty $property ) {

                return $property->name;

            },

            $properties

        );

    }

}