<?php
/**
 * Created by PhpStorm.
 * User: anthonybrown
 * Date: 2019-03-04
 * Time: 13:33
 */

namespace OdReviewForm\Core\WpOptions;


abstract class WpOption
{

    /**
     * @var string Unique key for the option. Required in child class unless getOptionName() is modified.
     * @see getOptionName()
     */
    protected $optionName;

    abstract public static function getInstance( bool $autoload = true ) : self;

    protected function __construct( bool $autoload = true )
    {

        if( $autoload )

            $this->load();

    }

    abstract public function load() : self;

    abstract public function save() : self;

    public function getOptionName() : string
    {

        return $this->optionName;

    }

}