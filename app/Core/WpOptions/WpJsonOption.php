<?php
/**
 * Created by PhpStorm.
 * User: ac524
 * Date: 2/22/2019
 * Time: 2:20 PM
 */

namespace OdReviewForm\Core\WpOptions;

use OdReviewForm\Core\Traits\ObjectProperties;

abstract class  WpJsonOption extends WpOption
{

    use ObjectProperties;

    /** @var bool */
    protected $hasLoaded;

    /**
     * @return WpJsonOption
     */
    public function load() : WpOption
    {

        if( null !== $this->hasLoaded )

            return $this;

        $this->hasLoaded = true;

        $optionStore = json_decode( get_option( $this->getOptionName(), '{}' ), true );

        $this->updateProperties( $optionStore );

//        foreach ( $this->getPublicProperties() as $propertyName )
//
//            if( isset( $optionStore->{ $propertyName } ) )
//
//                $this->{ $propertyName } = $optionStore->{ $propertyName };

        return $this;

    }

    /**
     * @return WpJsonOption
     */
    public function save() : WpOption
    {

        $optionStore = new \stdClass();

        foreach ( $this->getPublicProperties() as $propertyName )

            $optionStore->{ $propertyName } = $this->{ $propertyName };

        update_option( $this->getOptionName(), json_encode( $optionStore ) );

        return $this;

    }

}