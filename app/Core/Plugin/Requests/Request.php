<?php
/**
 * Created by PhpStorm.
 * User: ac524
 * Date: 3/28/2019
 * Time: 9:42 AM
 */

namespace OdReviewForm\Core\Plugin\Requests;

use OdReviewForm\Core\Collections\CollectionClass;

class Request extends CollectionClass {

	/** @var int|string filter_input_array() type or 'body' for getting data from the body content. */
	private $inputType;

	/** @var array List of property filters that conform to filter_input_array() */
	private $inputFilters;

	public function __construct( $inputType, array $inputFilters )
	{

		$this->inputType = $inputType;

		$this->inputFilters = $inputFilters;

	}

	/**
	 * @return int|string
	 */
	public function getInputType()
	{
		return $this->inputType;
	}

	/**
	 * @return array
	 */
	public function getInputFilters() : array
	{
		return $this->inputFilters;
	}


	public function isRequestMethod() : bool
	{

		return filter_input( INPUT_SERVER, 'REQUEST_METHOD' ) === $this->getRequestMethod();

	}

	public function getRequestMethod() : ?string
	{
		static $typeToMethodMap = [
			INPUT_POST => 'POST',
			INPUT_GET => 'GET',
			'body' => 'POST'
		];

		return $typeToMethodMap[ $this->getInputType() ] ?? null;

	}

	public function getRequestData() : array
	{

		if( 'body' === $this->getInputType() )

			return json_decode( file_get_contents('php://input'), true );

		$values = filter_input_array( $this->getInputType(), $this->getInputFilters() );

		foreach ( $this->getInputFilters() as $key => $filter ) {

			if( !is_array( $filter ) )

				continue;

			if( isset( $filter[ 'default' ] ) && !isset( $values[ $key ] ) )

				$values[ $key ] = $filter[ 'default' ];

		}

		return $values;

	}

}