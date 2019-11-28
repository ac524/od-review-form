<?php


namespace OdReviewForm\Core\Plugin\Html\Forms\Traits;


use OdReviewForm\Core\Plugin\Html\Forms\Form;

trait FormNonce {

	/**
	 * @return bool
	 */
	public function hasNonce() : bool
	{
		return $this->inputConfig['nonce'] ?? false;
	}

	/**
	 * @param string $nonceValue
	 *
	 * @return bool
	 */
	public function isNonceValid( ?string $nonceValue ) : bool
	{

		if( empty( $nonceValue ) )

			return false;

		$nonceState = \wp_verify_nonce( $nonceValue, $this->getNonceAction() );

		return 1 === $nonceState;

	}

	/**
	 * @return string|null
	 */
	public function getNonceName() : ?string
	{
		return $this->hasNonce() ? '_wpnonce_'. $this->id : null;
	}

	public function getNonceAction() : ?string
	{
		return  $this->hasNonce() ? 'CpForm'. $this->id .'Submit' : null;
	}

	/**
	 * @return Form|FormNonce
	 */
	public function addNonce() : self
	{

		$this->inputConfig['nonce'] = true;

		return $this;

	}

	/**
	 * @return Form|FormNonce
	 */
	public function removeNonce() : self
	{

		$this->inputConfig['nonce'] = false;

		return $this;

	}

	/**
	 * @return string - HTML for the wordpress nonce field if nonce is enabled or an empty string if not.
	 */
	protected function getNonce() : string
	{

		$nonceName = $this->getNonceName();

		if( empty( $nonceName ) )

			return '';

		return wp_nonce_field( $this->getNonceAction(),$this->getNonceName(), true, false );

	}

}