<?php

namespace OdReviewForm\Core\Request\Traits;


trait RequestStates {

	/**
	 * @return array
	 */
	public function healthyCodes() : array
	{
		return [ self::RESPONSE_SUCCESS, self::RESPONSE_CREATED ];
	}

	/**
	 * @return bool
	 */
	public function isHealthy() : bool
	{
		return in_array( $this->httpCode, $this->healthyCodes() );
	}

	/**
	 * @return bool
	 */
	public function isNotHealthy() : bool
	{
		return ! $this->isHealthy();
	}

	/**
	 * @return bool
	 */
	public function isCreated() : bool
	{
		return $this->httpCode === self::RESPONSE_CREATED;
	}

}