<?php

namespace OdReviewForm\Core\Request;

use OdReviewForm\Core\Request\Traits\RequestCurl;
use OdReviewForm\Core\Request\Traits\RequestStates;

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

	const API_URL = 'https://api-ssl.bitly.com/v4/';

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
		$this->method = $method;
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

}