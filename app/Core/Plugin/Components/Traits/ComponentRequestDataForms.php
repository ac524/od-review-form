<?php
/**
 * Created by PhpStorm.
 * User: ac524
 * Date: 3/28/2019
 * Time: 2:52 PM
 */

namespace OdReviewForm\Core\Plugin\Components\Traits;


use OdReviewForm\Core\Collections\MapClassCollection;
use OdReviewForm\Core\Exceptions\InvalidCollectionConfiguation;
use OdReviewForm\Core\Plugin\Exceptions\InvalidComponentConfiguration;
use OdReviewForm\Core\Plugin\Html\Forms\Form;
use OdReviewForm\Core\Plugin\Html\Forms\Forms;
use OdReviewForm\Core\Plugin\Interfaces\RequestDataInterface;
use OdReviewForm\Core\Plugin\Requests\Request;
use OdReviewForm\Core\Traits\ImplementsInterfaces;

/**
 * Trait ComponentRequestDataForms
 * @package ComposerPress\Core\Plugin\Components\Traits
 *
 * @method bool implements( string $interfaceClassName )
 * @see ImplementsInterfaces::implements()
 *
 */
trait ComponentRequestDataForms {

	/** @var Forms */
	private $forms;

	public function getForms() : Forms
	{

		if( null === $this->forms )

			$this->forms = new Forms();

		return $this->forms;

	}

	/**
	 * @param string $id
	 * @param $inputType
	 * @param array $inputSettings
	 *
	 * @return ComponentRequestDataForms
	 * @throws InvalidComponentConfiguration
	 */
	public function addForm( string $id, $inputType, array $inputSettings ) : self
	{

		if( !$this->implements( RequestDataInterface::class ) )

			throw new InvalidComponentConfiguration( 'Components adding forms must implement '. RequestDataInterface::class );


		try {

			/** @var Form $form */
			$form = $this->getForms()->itemFactory( $id, $inputSettings );

		} catch( InvalidCollectionConfiguation $exception ) {

			// TODO Error log and safe exit.
			die( $exception->getMessage() );

		}

		/** @var Request $request */
		$request = $this
			->addRequest( $id, $inputType, $form->getInputFilters() )
			->getRequests()
			->last();

		$form->setMethod( $request->getRequestMethod() );

		$this->getForms()->set( $id, $form );

		return $this;

	}

}