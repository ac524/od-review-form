<?php
/**
 * Created by PhpStorm.
 * User: ac524
 * Date: 2/21/2019
 * Time: 5:48 PM
 */

namespace OdReviewForm\Core\Plugin\Components\Traits;

use OdReviewForm\Core\Collections\MapClassCollection;
use OdReviewForm\Core\Exceptions\InvalidCollectionConfiguation;
use OdReviewForm\Core\Plugin\Exceptions\InvalidRequestDataConfiguration;
use OdReviewForm\Core\Plugin\Interfaces\RequestDataInterface;
use OdReviewForm\Core\Plugin\Requests\Request;
use OdReviewForm\Core\Plugin\Requests\Requests;

/**
 * Trait ComponentRequestData
 * @package ComposerPress\Plugin\Components\Traits
 *
 * @property int|string $inputType filter_input_array() type or 'body' for getting data from the body content.
 * @property array $inputArrayFilters List of property filters that conform to filter_input_array();
 * @property bool $isNonceEnabled Flag to validate nonces.
 *
 * @see \filter_input_array()
 *
 */
trait ComponentRequestData
{

	/** @var string|null */
	private $activeRequestKey;

	/** @var Request|false */
	private $activeRequest;

	/** @var Requests */
	private $requests;

	/**
	 * Use addRequest() to add new Requests to the collection.
	 */
	abstract function registerRequests() : void;

	/**
	 * @return Requests
	 */
	public function getRequests() : Requests
	{

		if( null === $this->requests )

			$this->requests = new Requests();

		return $this->requests;

	}

	/**
	 * @param string $id
	 * @param $inputType
	 * @param array $inputArrayFilters
	 *
	 * @return ComponentRequestData|RequestDataInterface
	 *
	 * @see RequestDataInterface::addRequest()
	 *
	 */
	public function addRequest( string $id, $inputType, array $inputArrayFilters ) : RequestDataInterface
	{

		try {

			$this->getRequests()->set(

				$id,

				$this->getRequests()->itemFactory( $inputType, $inputArrayFilters )

			);

		} catch( InvalidCollectionConfiguation $exception ) {

			die( $exception->getMessage() );

		}

		return $this;

	}

    /**
     * Validates the current request against the defined data component.
     * @return bool
     *
     * @see RequestDataInterface::hasActiveRequest()
     */
    public function hasActiveRequest() : bool
    {
        return !empty( $this->getActiveRequest() );
    }

    /**
     * @return array
     */
    public function getRequestData() : array
    {
        return $this->getActiveRequest()->getRequestData();
    }

    public function getRequestDataValue( string $name ) : ?string
    {
    	return $this->getRequestData()[ $name ] ?? null;
    }

	/**
	 * @return Request|object|null
	 */
    public function getActiveRequest() : ?Request
    {

    	$this->setActiveRequest();

    	return false === $this->activeRequest

		    ? null

		    : $this->activeRequest;

    }

	/**
	 * @return string|null
	 */
    public function getActiveRequestKey() : ?string
    {
    	return $this->activeRequestKey;
    }

    /**
     * @return int|string 'body' or filter_input_array() input type
     * @see \filter_input_array()
     */
    protected function getInputType()
    {

        return $this->inputType ?? INPUT_POST;

    }

    /**
     * @return array
     */
    protected function getInputFilters() : array
    {

        return $this->inputArrayFilters ?? [];

    }

    /**
     * @return bool|int
     * @throws InvalidRequestDataConfiguration
     */
    protected function isValidNonce()
    {

        if( 'body' === $this->getInputType() )

            throw new InvalidRequestDataConfiguration( 'POST Body type requests cannot validate nonces.' );

        return \wp_verify_nonce( $this->getNonce(), $this->getSlug() );

    }

    protected function isActiveRequest( string $key, Request $request )
    {
        return "1" === filter_input( $request->getInputType(), $key.'Submit' );
    }

    private function getNonce()
    {

        return filter_input( $this->getInputType(), '_wpnonce_'. $this->getSlug() );

    }

    private function setActiveRequest() : void
    {

	    if( false === $this->activeRequest )

		    return;

	    if( null !== $this->activeRequest )

		    return ;

	    foreach( $this->requests->all() as $key => $request ) {

		    // Validate the request method.
		    if( !$request->isRequestMethod() )

			    continue;

		    // Validate a unique data entry
		    if( $this->isActiveRequest( $key, $request ) ) {

		    	$this->activeRequestKey = $key;
		    	$this->activeRequest = $request;

		    }

	    }

	    if(  empty( $this->activeRequest ) ) {

		    $this->activeRequestKey = null;
		    $this->activeRequest = false;

	    }

    }

}