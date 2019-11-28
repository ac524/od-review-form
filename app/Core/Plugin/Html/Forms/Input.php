<?php
/**
 * Created by PhpStorm.
 * User: anthonybrown
 * Date: 2/24/19
 * Time: 6:32 PM
 */

namespace OdReviewForm\Core\Plugin\Html\Forms;

use OdReviewForm\Core\Collections\CollectionClass;
use OdReviewForm\Core\Interfaces\HtmlOutputInterface;
use OdReviewForm\Core\Traits\HtmlOutput;

class Input extends CollectionClass implements HtmlOutputInterface
{

    use HtmlOutput;

    private $name;

    private $value;

    private $label;

    /** @var callable|string */
    private $validationMessage;

    private $type = 'text';

    private $disabled = false;

    /**
     * InputHtml constructor.
     * @param string $name
     * @param $value
     * @param array $config
     */
    public function __construct( string $name, $value, array $config = [] )
    {

        $this->name = $name;

        $this->value = $value;

        if( isset( $config[ 'label' ] ) )

            $this->label = $config[ 'label' ];

        if( isset( $config[ 'validationMessage' ] ) )

            $this->validationMessage = $config[ 'validationMessage' ];

        if( isset( $config[ 'type' ] ) )

            $this->type = $config[ 'type' ];

        if( isset( $config[ 'disabled' ] ) )

            $this->disabled = $config[ 'disabled' ];

    }

    public function getHtml(): string
    {

    	$html = '';

    	if( !$this->isHidden() )

    		$html .= $this->getFieldHeader();

        if( $this->type === 'textarea' )

	        $html .= $this->getTextArea();

        else

        	$html .= $this->getInput();


	    if( !$this->isHidden() )

		    $html .= $this->getFieldFooter();

        return $html;

    }

    protected function getFieldHeader() : string
    {
	    $html = '<div class="form-field">';

	    if( !empty( $this->label ) )

		    $html .= '<label for="'. $this->name .'">'. $this->label .'</label>';

	    return $html;
    }

	protected function getFieldFooter() : string
	{
		$html = '';

		$validationMessage = $this->getValidationMessage();

		if( !empty( $validationMessage ) )

			$html .= '<p class="alert alert-error">'. $validationMessage .'</p>';

		$html .= '</div>';

		return $html;
	}

    protected function getInput() : string
    {
    	return sprintf(
		    '<input type="%s" name="%s" value="%s"%s%s />',
		    $this->type,
		    $this->name,
		    $this->getInputValue(),
		    $this->getChecked(),
            $this->getDisabled()
	    );
    }

    protected function getTextArea()
    {
	    return sprintf(
		    '<textarea name="%s">%s</textarea>',
		    $this->name,
		    $this->getInputValue()
	    );
    }

    protected function setType( string $type )
    {

        $this->type = $type;

        if( 'checkbox' === $this->type && !is_int( $this->value ) )

            $this->value = (int)$this->value;

    }

    protected function isHidden() : bool
    {
    	return $this->type === 'hidden';
    }

    private function getChecked() : ?string
    {

        if( 'checkbox' !== $this->type || !$this->value )

            return null;

        return ' checked="checked"';

    }

    private function getDisabled() : ?string
    {

        if( ! $this->disabled )

            return null;

        return ' disabled="disabled"';

    }

    private function getInputValue()
    {

        if( 'checkbox' === $this->type )

            return 1;

        return $this->value;

    }

    private function getValidationMessage() : ?string
    {

        if( empty( $this->validationMessage ) )

            return null;

        if( is_callable( $this->validationMessage ) )

            return call_user_func( $this->validationMessage, $this->value );

        return $this->validationMessage;

    }

}