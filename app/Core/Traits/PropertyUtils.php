<?php

namespace OdReviewForm\Core\Traits;

use ArrayObject;
use ReflectionClass;
use ReflectionProperty;

trait PropertyUtils
{

    private $propUtilsReflection;

    /**
     * Helper function for setting all known public and properties via an array
     */
    protected function setPropertyValues( array $propertyValues ) : void
    {
        $propertyValueLoop = (new ArrayObject( array_intersect_key( $propertyValues, array_flip( $this->properties() ) ) ))->getIterator();

        while( $propertyValueLoop->valid() ) {

            $this->{ $propertyValueLoop->key() } = $propertyValueLoop->current();

            $propertyValueLoop->next();

        }
    }

    protected function properties() : array
    {

        return $this->helperPropertyNames( array_diff(

            $this->propUtilsReflection()->getProperties(),

            $this->propUtilsReflection()->getProperties(ReflectionProperty::IS_STATIC )

        ) );

    }

    protected function publicProperties() : array
    {
        return $this->helperPropertyNames( array_diff(

            $this->propUtilsReflection()->getProperties( ReflectionProperty::IS_PUBLIC ),

            $this->propUtilsReflection()->getProperties(ReflectionProperty::IS_STATIC )

        ) );

    }

    protected function propertyValue( string $property )
    {
        return $this->{ $property };
    }

    private function propUtilsReflection() : ?ReflectionClass
    {
        if( null === $this->propUtilsReflection )

            try {

                $this->propUtilsReflection = new ReflectionClass($this);

            } catch( \Exception $exception ) {

                $this->propUtilsReflection = false;
                // Continue

            }

        return $this->propUtilsReflection ?: null;
    }

    /**
     * @param \ReflectionProperty[] $properties
     * @return array
     */
    private function helperPropertyNames( array $properties )
    {

        return array_map(

            function( \ReflectionProperty $property ) {

                return $property->name;

            },

            $properties

        );

    }

}