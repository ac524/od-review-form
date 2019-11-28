<?php


namespace OdReviewForm\OddDog\FormsApiClient\Traits;

use OdReviewForm\OddDog\FormsApiClient\Request;

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
	    $url = $this->url();

        $curlData = $this->curlData();
        $hasData = ! empty( $curlData );
        $isQueryArgsData = is_array( $curlData );

        if( $hasData  &&  $isQueryArgsData )

                $url = add_query_arg( $curlData, $url );

		$this->curl = curl_init( $url );

		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

		curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $this->method);

        curl_setopt($this->curl, CURLOPT_VERBOSE, true);

        $curlData = $this->curlData();

		if( $hasData && ! $isQueryArgsData )

			curl_setopt( $this->curl, CURLOPT_POSTFIELDS, $curlData );

		return $this->setHeaders();
	}

    /**
     * @return array|string|null
     */
	protected function curlData()
    {
        $data = $this->data ?? [];

        if( ! empty( $this->apiToken ) )

            $data['token'] = $this->apiToken;

        if( empty( $data ) )

            return null;

        return $this->method === 'GET' ? $data : json_encode( $data );
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
	    if( $this->result )

	        $this->result = json_decode( $this->result, true );

		return $this;
	}

	/**
	 * @return Request|RequestCurl
	 */
	protected function setHeaders() : Request
	{
		$headers = [
			'Content-Type: application/json',
			'Accept: application/json',
            'Cache-Control: no-cache'
		];

		if( ! empty( $headers ) )

			curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers );

		return $this;
	}

}