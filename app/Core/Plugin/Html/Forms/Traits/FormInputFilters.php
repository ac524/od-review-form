<?php


namespace OdReviewForm\Core\Plugin\Html\Forms\Traits;


use OdReviewForm\Core\Plugin\Exceptions\InvalidComponentConfiguration;

trait FormInputFilters {

	/**
	 * @return array
	 * @throws InvalidComponentConfiguration
	 */
	public function getInputFilters() : array
	{

		$inputArrayFilters = [];

		foreach ( $this->inputConfig as $inputName => $inputConfig ) {

			if( $this->isSettingName( $inputName ) )

				continue;

			$inputType = $inputConfig['type'] ?? 'text';

			$inputArrayFilters[ $inputName ] = $inputConfig['filter'] ?? $this->getInputTypeFilter( $inputType );

		}

		if( $this->hasNonce() )

			$inputArrayFilters[ $this->getNonceName() ] = FILTER_SANITIZE_STRING;

		return $inputArrayFilters;

	}

	/**
	 * @param string $type
	 *
	 * @return mixed
	 * @throws InvalidComponentConfiguration
	 */
	private function getInputTypeFilter( string $type )
	{

		static $inputTypeMap = [
			'text' => [
				'filter' => FILTER_SANITIZE_STRING,
				'flags' => FILTER_FLAG_NO_ENCODE_QUOTES
			],
			'textarea' => [
				'filter' => FILTER_SANITIZE_STRING,
				'flags' => FILTER_FLAG_NO_ENCODE_QUOTES
			],
			'hidden' => FILTER_SANITIZE_STRING,
			'checkbox' => [
				'filter' => FILTER_VALIDATE_BOOLEAN,
				'default' => false
			],
			'number' => FILTER_SANITIZE_NUMBER_INT
		];

		if( !isset( $inputTypeMap[ $type ] ) )

			throw new InvalidComponentConfiguration( 'Added unknown form input type.' );

		return  $inputTypeMap[ $type ];

	}


}