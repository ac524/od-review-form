<?php

namespace OdReviewForm\Core\Request\Traits;

use OdReviewForm\Bitly\Request;

trait RequestCurl {

	/**
	 * @return Request|RequestCurl
	 */
	public function execute() : Request
	{
		return
			$this
				->init()
				->send()
				->record()
				->close()
				->handle();
	}


	/**
	 * Initialize the Request.
	 *
	 * @return Request|RequestCurl
	 */
	protected function init() : Request
	{
		$this->curl = curl_init( $this->url() );

		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

		curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $this->method);

		if( ! empty( $this->data ) )

			curl_setopt( $this->curl, CURLOPT_POSTFIELDS, json_encode( $this->data ) );

		return $this->setHeaders();
	}

	/**
	 * Send the request.
	 *
	 * @return Request|RequestCurl
	 */
	protected function send() : Request
	{
		$this->result = curl_exec( $this->curl );

		return $this;
	}

	/**
	 * Record request information before the resource closes.
	 *
	 * @return Request|RequestCurl
	 */
	protected function record() : Request
	{
		$info = curl_getinfo( $this->curl );

		$this->httpCode = (int)$info['http_code'];

		return $this;
	}

	/**
	 * Close request resources.
	 *
	 * @return Request|RequestCurl
	 */
	protected function close() : Request
	{
		curl_close( $this->curl );

		return $this;
	}

	/**
	 * Handle the results of the request.
	 *
	 * @return Request|RequestCurl
	 */
	protected function handle() : Request
	{
		if( ! $this->isHealthy() )

			return $this;

		$this->result = json_decode( $this->result );

		return $this;
	}

	/**
	 * @return Request|RequestCurl
	 */
	protected function setHeaders() : Request
	{
		$headers = [
			'Content-Type: application/json',
			'Accept: application/json'
		];

		if( ! empty( $this->apiToken ) )

			$headers[] = 'Authorization: Bearer '. $this->apiToken;

		if( ! empty( $headers ) )

			curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers );

		return $this;
	}

}