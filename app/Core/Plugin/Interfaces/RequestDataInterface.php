<?php
/**
 * Created by PhpStorm.
 * User: ac524
 * Date: 2/22/2019
 * Time: 4:01 PM
 */

namespace OdReviewForm\Core\Plugin\Interfaces;

use OdReviewForm\Core\Plugin\Requests\Requests;

/**
 * Interface RequestDataInterface
 * @package ComposerPress\Core\Plugin\Interfaces
 */
interface RequestDataInterface
{

	/**
	 * Register built in requests.
	 */
	public function registerRequests() : void;

	/**
	 * Return the current collection of known requests.
	 * @return Requests
	 */
	public function getRequests() : Requests;

	/**
	 * Add a Request instance to an internal collection.
	 * @return RequestDataInterface
	 */
	public function addRequest( string $id, $inputType, array $inputArrayFilters ) : self;

    /**
     * Check the current collection for a Request instance that matches the current request.
     * @return bool
     */
    public function hasActiveRequest() : bool;

	/**
	 * Process data from the active request.
	 */
    public function processRequestData() : void;

	/**
	 * Get data from the active request.
	 * @return array
	 */
    public function getRequestData() : array;

}