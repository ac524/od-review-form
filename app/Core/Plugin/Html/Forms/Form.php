<?php
/**
 * Created by PhpStorm.
 * User: anthonybrown
 * Date: 2/24/19
 * Time: 6:14 PM
 */

namespace OdReviewForm\Core\Plugin\Html\Forms;


use OdReviewForm\Core\Collections\CollectionClass;
use OdReviewForm\Core\Interfaces\HtmlOutputInterface;
use OdReviewForm\Core\Plugin\Html\Forms\Traits\FormInputFilters;
use OdReviewForm\Core\Plugin\Html\Forms\Traits\FormNonce;
use OdReviewForm\Core\Traits\HtmlOutput;
use OdReviewForm\Core\WpOptions\WpJsonOption;

/**
 * Class FormHtml
 * @package ComposerPress\Plugin\Html\Form
 */
class Form extends CollectionClass implements HtmlOutputInterface
{

	use FormNonce;
	use FormInputFilters;

    use HtmlOutput;

    /** @var string */
    protected $method = 'POST';

    /** @var string */
    protected $action;

    protected $isSubmitProtected;

    private $id;

    /** @var WpJsonOption|CollectionClass|null */
    private $inputValues;

    /** @var  Input[] */
    private $inputs;

	/** @var  array */
	private $inputConfig;

	/**
	 * Form constructor.
	 *
	 * @param string $id
	 * @param array|null $inputConfig
	 * @param WpJsonOption|null $inputValues
	 */
    public function __construct( string $id, ?array $inputConfig = null, $inputValues = null )
    {

        $this->id = $id;

        if( !empty( $inputValues ) )

            $this->inputValues = $inputValues;

        $this->inputConfig = $inputConfig ?? [];

	    $this->inputConfig += [
            'nonce' => false,
	        'submit' => 'Update'
        ];

    }

	/**
	 * @param WpJsonOption|CollectionClass $inputValues
	 *
	 * @return Form
	 */
    public function setInputValues( $inputValues ) : self
    {

    	$this->inputValues = $inputValues;

    	return $this;

    }

	/**
	 * @return string
	 */
	public function getHtml(): string
    {

        return $this->getHeaderHtml() . $this->getBodyHtml() . $this->getFooterHtml();

    }

    public function getHeaderHtml() : string
    {
        return

        '<div class="form-wrap">'

            . '<form method="'. $this->getMethod() .'" action="'. $this->getAction() .'"'. $this->getSubmitProtection() .'>';
    }

    public function getBodyHtml() : string
    {
        if( empty( $this->inputs ) )

            $this->setInputs();

        return $this->getBody();
    }

    public function getFooterHtml() : string
    {
        return $this->getFooter()

            . '</form>'

        . '</div>';
    }

    public function getInputNames() : array
    {

	    return array_diff( array_keys( $this->inputConfig ), $this->getInputConfigSettingNames() );

    }

	/**
	 * @param string $name
	 * @param array $config
	 *
	 * @return Form
	 */
    public function addInput( string $name, array $config = [] ) : self
    {
    	if( ! isset( $this->inputConfig[$name] ) )

		    $this->inputConfig[$name] = $config;

	    return $this;
    }

    public function removeInput( string $name ) : self
    {
        if( isset( $this->inputConfig[$name] ) )

            unset( $this->inputConfig[ $name ] );

        return $this;
    }

    public function updateInputConfig( string $name, array $options ) : self
    {
        if( isset( $this->inputConfig[$name] ) )

            $this->inputConfig[$name] = $options + $this->inputConfig[$name];

        return $this;
    }

    public function updateSetting( $key, $value ) : self
	{
		if( $this->isSettingName( $key ) )

			$this->inputConfig[$key] = $value;

		return $this;
    }

    public function hasInputValues()
    {

    	return !empty( $this->inputValues );

    }

    /**
     * @param $name
     * @return mixed
     */
    public function getInputValue( $name )
    {

    	if( ! $this->hasInputValues() )

    		return null;

    	if( is_callable( [ $this->inputValues, 'getPropertyValue' ] ) )

    		return $this->inputValues->getPropertyValue( $name );

        return $this->inputValues->{ $name } ?? null;

    }

	public function setMethod( string $method ) : self
	{
		$this->method = $method;

		return $this;
	}

	public function protectSubmit( bool $protect = true ) : self
	{
		$this->isSubmitProtected = $protect;

		return $this;
	}

	protected function getSubmitProtection() : string
	{
		if( ! $this->isSubmitProtected )

			return '';

		return ' onclick="confirm( \'Are you Sure?\' ) ? null : event.preventDefault(), event.stopPropagation()"';
	}

	public function setSubmitText( string $text ) : self
    {
        $this->inputConfig['submit'] = $text;

        return $this;
    }

	/**
	 * @param array|null $inputConfig
	 *
	 * @return Form
	 */
	protected function setInputs( ?array $inputConfig = null ) : self
	{

		if( null === $this->inputs )

			foreach ( ( $inputConfig ?? $this->inputConfig ) as $name => $config ) {

				// Don't add setting key names as inputs.
				if( $this->isSettingName( $name ) )

					continue;

				$this->setInput( $name, $config );

			}


		return $this;

	}

	protected function setInput( string $name, array $config = [] )
	{
		$this->inputs[] = new Input( $name, $this->getInputValue( $name ), $config );
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	protected function isSettingName( string $name ) : bool
	{
		return in_array( $name, $this->getInputConfigSettingNames() );
	}

	protected function getInputConfigSettingNames() : array
	{

		/**
		 * Names of input configurations that are settings instead of input definitions.
		 */
		static $settingNames = [
			'nonce',
			'submit'
		];

		return $settingNames;

	}

    protected function getMethod() : string
    {

        return $this->method;

    }

    protected function getAction()
    {

        return $this->action;

    }

    protected function getBody() : string
    {

    	if( empty( $this->inputs ) )

    		return '';

        return implode( "\n\t", $this->inputs );

    }

    protected function getFooter() : string
    {

        $html = '<div class="form-field">'

            . $this->getNonce()

            . '<input type="hidden" name="'. $this->id .'Submit" value="1" />'

            . '<button class="button button-primary" role="submit">'. $this->getSubmitText() .'</button>'

        .'</div>';

        return $html;

    }

    protected function getSubmitText() : string
    {
    	return $this->inputConfig['submit'];
    }

}