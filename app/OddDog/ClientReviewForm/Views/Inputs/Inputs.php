<?php

namespace OdReviewForm\OddDog\ClientReviewForm\Views\Inputs;

class Inputs extends \OdReviewForm\Core\Views\Inputs\Inputs
{

    protected static $instance;

    protected function __construct()
    {
        parent::__construct();

        $this->assignStyleNamespace( 'od' );
    }
}