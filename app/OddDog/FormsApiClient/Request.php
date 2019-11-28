<?php


namespace OdReviewForm\OddDog\FormsApiClient;

use OdReviewForm\OddDog\FormsApiClient\Traits\RequestCurl;
use OdReviewForm\OddDog\FormsApiClient\Traits\RequestStates;

class Request {

	use RequestCurl;
	use RequestStates;

	const RESPONSE_SUCCESS = 200;
	const RESPONSE_CREATED = 201;
	const RESPONSE_BAD_REQUEST = 400;
	const RESPONSE_UPGRADE_REQUIRED = 402;
	const RESPONSE_FORBIDDEN = 403;
	const RESPONSE_NOT_FOUND = 404;
	const RESPONSE_EXPECTATION_FAILED = 417;
	const RESPONSE_UNPROCESSABLE_ENTITY = 422;
	const RESPONSE_INTERNAL_ERROR = 500;
	const RESPONSE_TEMPORARILY_UNAVAILABLE = 503;

	const API_URL = 'https://odd.dog/reviews/wp-json/odreviewforms/v1/';

	protected $endpoint;

	protected $data;

	protected $curl;

	protected $apiToken;

	protected $result;

	protected $httpCode;

	/**
	 * Request constructor.
	 *
	 * @param $endpoint
	 * @param $data
	 * @param string $method
	 */
	public function __construct( $endpoint, $data, $method = "GET" )
	{
		$this->endpoint = $endpoint;
		$this->data = $data;
		$this->method = strtoupper($method);
	}

	/**
	 * @return string
	 */
	public function url()
	{
		return self::API_URL . $this->endpoint;
	}

	/**
	 * @param $token
	 *
	 * @return Request
	 */
	public function setToken( $token ) : self
	{
		$this->apiToken = $token;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function result()
	{
		return $this->result;
	}

    public function getErrorCodes() : array
    {
        if( $this->isHealthy() )

            return [];

//        if( isset( $this->result['data'] ) && ! empty( $this->result['data']['params'] ) )
//
//            $this->result['data']['params'];

        return isset( $this->result['code'] ) ? [ $this->result['code'] ] : [ 'error' ];
    }

	public function getErrorMessages() : array
    {
        if( $this->isHealthy() )

            return [];

        if( isset( $this->result['data'] ) && ! empty( $this->result['data']['params'] ) )

            $this->result['data']['params'];

        return isset( $this->result['message'] ) ? [ $this->result['message'] ] : [ 'Error' ];
    }

}