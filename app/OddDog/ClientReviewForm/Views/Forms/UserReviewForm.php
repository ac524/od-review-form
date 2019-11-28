<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Views\Forms;


use OdReviewForm\Core\Interfaces\HtmlOutputInterface;
use OdReviewForm\Core\Traits\HtmlOutput;
use OdReviewForm\OddDog\ClientReviewForm\Locations\Location;
use OdReviewForm\OddDog\ClientReviewForm\Locations\Locations;
use OdReviewForm\OddDog\ClientReviewForm\Reviews\Review;
use OdReviewForm\OddDog\ClientReviewForm\Reviews\Reviews;
use OdReviewForm\OddDog\ClientReviewForm\Views\Forms\Inputs\RatingInput;
use OdReviewForm\OddDog\ClientReviewForm\Views\OddDogLinkBack;

class UserReviewForm implements HtmlOutputInterface
{
    use HtmlOutput;

    private $inputs;

    private $index;

    private $errors;

    private $data;

    private $hasSubmission = false;

    private static $count;

    /**
     * UserReviewForm constructor.
     * @param string|null $location
     */
    public function __construct( ?string $location = null )
    {
        self::$count++;

        $this->index = self::$count;

        $this->inputs = [];

        $this
            ->addInput([
                'label' => 'Rating',
                'type' => 'rating',
                'name' => 'rating',
                'required' => true,
                'filterInput' => FILTER_VALIDATE_INT
            ])
            ->addInput([
                'label' => 'Name',
                'type' => 'text',
                'name' => 'reviewer',
                'placeholder' => 'Your Name',
                'required' => true,
                'filterInput' => FILTER_SANITIZE_STRING
            ])
            ->addInput([
                'label' => 'Email',
                'type' => 'email',
                'name' => 'email',
                'placeholder' => 'Your Email',
                'required' => true,
                'filterInput' => FILTER_VALIDATE_EMAIL
            ]);



        if( ! empty( $location ) ) {

            if( !Locations::instance()->containsKey( $location ) ) {

                $this->addError( 'location', 'Please provide a valid location from the following list: '. implode( ', ', Locations::instance()->keys() ) );
                return;

            }

            $this
                ->addInput([
                    'type' => 'hidden',
                    'name' => 'location',
                    'value' => $location,
                    'filterInput' => FILTER_SANITIZE_STRING
                ]);

        } else {

            $locations = Locations::instance();
            $locationsCount = $locations->count();

            if( $locationsCount > 0 ) {

                if( $locationsCount > 1 )

                    $this->addInput([
                        'label' => 'Location',
                        'type' => 'select',
                        'name' => 'location',
                        'options' => $locations->names(),
                        'required' => true,
                        'filterInput' => FILTER_SANITIZE_STRING,
                        'sanitizeUrlInput' => function( $value ) {

                            return Location::idFactory( $value );

                        }
                    ]);

                else {

                    $singleLocation = $locations->first();

                    if( 'default' !== $singleLocation->id() )

                        $this->addInput([
                            'type' => 'hidden',
                            'name' => 'location',
                            'value' => $singleLocation->id(),
                            'filterInput' => FILTER_SANITIZE_STRING
                        ]);

                }

            }

        }

        $this->addInput([
                'label' => 'Message',
                'type' => 'textarea',
                'name' => 'reviewMessage',
                'placeholder' => 'Tell us about your experience',
                'required' => true,
                'filterInput' => FILTER_SANITIZE_STRING
            ]);

        $this->errors = [];

        $this->maybeLoadPostData();
    }

    public function inputFilters() : array
    {
        $inputFilters =  array_map( function( $input ) { return $input['filterInput']; }, $this->inputs );

        $inputFilters[ 'odrfIndex' ] = FILTER_VALIDATE_INT;

        return $inputFilters;
    }

    /**
     * @param array $inputOptions
     * @return UserReviewForm
     */
    public function addInput( array $inputOptions ) : self
    {
        $inputOptions += [
            'label' => '',
            'required' => false,
            'filterInput' => FILTER_DEFAULT,
            'sanitizeUrlInput' => null
        ];

        $this->inputs[ $inputOptions['name'] ] = $inputOptions;

        return $this;
    }

    /**
     * @return string
     */
    public function getHtml(): string
    {

        $id = 'odrf-'.  $this->index;

        $formHtml = sprintf('<div class="odrf-wrap" id="%s"><form method="post">', $id);

        $formHtml .= sprintf('<input type="hidden" name="odrfIndex" value="%s">', $this->index);

        $formHtml .= implode( "\n", array_map( [ $this, 'getInputHtml' ], $this->inputs ) );

        $formHtml .= '<button type="submit">Submit</button>';

        $formHtml .= '</form>';

        $formHtml .= '<div class="odrf-footer">'. (new OddDogLinkBack()) .'</div>';

        $formHtml .= '</div>';

        $formHtml .= $this->initScript( $id );

        return $formHtml;
    }

    /**
     * @param string $id
     * @return string
     */
    public function initScript( string $id ) : string
    {
        return sprintf( '<script type="text/javascript">odrfControls("%s")</script>', $id );
    }

    /**
     * @param $input
     * @return string
     */
    public function getInputHtml( $input )
    {
        static $noDisplay = [ 'hidden' ];

        $input += [
            'type' => 'text',
            'required' => false,
            'placeholder' => ''
        ];

        $displayable = ! in_array( $input['type'], $noDisplay );

        $inputHtml = '';

        if( $displayable ) {

            $inputHtml .= sprintf( '<div class="odrf-group odrf-field-%s">',  $input['name'] );

            $inputHtml .= sprintf( '<label>%s%s</label>', $input['label'], $this->requiredLabelMarker( $input['required'] ) );

        }

        $inputBuilder = [ $this, 'get'. ucfirst( $input['type'] ) .'InputHtml' ];

        $inputHtml .= is_callable( $inputBuilder )

            ? call_user_func( $inputBuilder, $input )

            : $this->getTextInputHtml( $input );

        if( $displayable ) {

            $inputHtml .= $this->getInputErrorMessage($input['name']);

            $inputHtml .= '</div>';

        }

        return $inputHtml;
    }

    /**
     * @param $name
     * @return string|null
     */
    public function getInputAttr( $name, $attr ) : ?string
    {
        return isset( $this->inputs[ $name ] ) ? $this->inputs[ $name ][ $attr ] : null;
    }

    /**
     * @return int
     */
    public function index() : int
    {
        return $this->index;
    }

    public function hasSubmission() : bool
    {
        return $this->hasSubmission;
    }

    /**
     * @param $name
     * @param $message
     */
    public function addError( $name, $message )
    {
        $this->errors[ $name ] = $message;
    }

    /**
     * @return bool
     */
    public function hasErrors() : bool
    {
        return ! empty( $this->errors );
    }

    public function data() : ?array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData( array $data )
    {
        $this->data = $data;
    }

    /**
     * @param $name
     * @return string
     */
    protected function getInputErrorMessage( $name ) {
        return isset( $this->errors[ $name ] )

            ? sprintf( '<div class="odrf-alert odrf-error">%s</div>', $this->errors[ $name ] )

            : '';
    }

    /**
     * @param array $input
     * @return string
     */
    protected function getTextInputHtml( array $input ) : string
    {
        return sprintf(
            '<input class="odrf-control" type="%s" name="%s"%s%s value="%s">',
            $input['type'],
            $input['name'],
            $this->requiredAttr( $input['required'] ),
            $this->placeholderAttr( $input['placeholder'] ),
            $this->value( $input['name'] )
        );
    }

    /**
     * @param array $input
     * @return string
     */
    protected function getTextareaInputHtml( array $input ) : string
    {
        return sprintf(
            '<textarea class="odrf-control" name="%s"%s%s>%s</textarea>',
            $input['name'],
            $this->requiredAttr( $input['required'] ),
            $this->placeholderAttr( $input['placeholder'] ),
            $this->value( $input['name'] )
        );
    }

    /**
     * @param array $input
     * @return string
     */
    protected function getSelectInputHtml( array $input ) : string
    {
        $currentValue = $this->value( $input['name'] );

        $optionHtml = implode( '', array_map( function( $label, $value ) use ( $currentValue ) {

            $selected = $currentValue === $value ? ' selected' : '';

            return sprintf( '<option value="%s"%s>%s</option>', $value, $selected, $label );

        }, $input[ 'options' ], array_keys( $input[ 'options' ] ) ) );

        return sprintf(
            '<select class="odrf-control" name="%s"%s>%s</select>',
            $input['name'],
            $this->requiredAttr( $input['required'] ),
            $optionHtml
        );
    }

    /**
     * @param array $input
     * @return string
     */
    protected function getRatingInputHtml( array $input ) : string
    {
        return sprintf(
            new RatingInput(),
            $input['name']
        );
    }

    /**
     * @param bool $required
     * @return string
     */
    protected function requiredLabelMarker( bool $required ) : string
    {
        return $required ? ' <span class="odrf-required-marker">*</span>' : '';
    }

    /**
     * @param bool $required
     * @return string
     */
    protected function requiredAttr( bool $required ) : string
    {
        return $required ? ' required="required"' : '';
    }

    /**
     * @param string $placeholder
     * @return string
     */
    protected function placeholderAttr( string $placeholder ) : string
    {
        return $placeholder ? ' placeholder="'. $placeholder .'"' : '';
    }

    /**
     * @param string $name
     * @return string
     */
    protected function value( string $name ) : string
    {
        $hasData = ! empty( $this->data ) && ! empty( isset( $this->data[ $name ] ) );

        if( $hasData )

            return (string)$this->data[ $name ];

        $data = filter_input( INPUT_GET, $name, $this->inputs[ $name ]['filterInput'] );

        if( ! empty( $data ) ) {

            if( is_callable( $this->inputs[$name]['sanitizeUrlInput'] ) )

                $data = $this->inputs[$name]['sanitizeUrlInput']( $data );

        }

        if( empty( $data ) && ! empty( $this->inputs[$name]['value'] ) )

            $data = $this->inputs[$name]['value'];

        return empty( $data ) ? '' : (string)$data;
    }

    /**
     *
     */
    protected function maybeLoadPostData() : void
    {
        if( ! $this->isPost() )

            return;

        $this->setData( filter_input_array( INPUT_POST, $this->inputFilters() ) );

        if( ! empty( $this->data ) && $this->data['odrfIndex'] === $this->index() ) {

            $this->hasSubmission = true;
            $this->validateData();

        }


    }

    /**
     * @return bool
     */
    protected function isPost() : bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     *
     */
    protected function validateData() : void
    {
        foreach( $this->data as $name => $value ) {

            if( false === $value ) {
                $this->addError( $name,  'Please provide a valid '. $this->getInputAttr( $name, 'label' ) );
                continue;
            }


            if( empty( $value ) )

                $this->addError( $name,  'This field is required' );

            if( 'email' === $name && $this->emailHasReview( $value ) )

                $this->addError( $name,  'This email already exists' );
        }
    }

    protected function emailHasReview( string $email )
    {
        $reviews = new Reviews();

        $queryConfig = $reviews->newQueryConfig();

        $queryConfig['meta_key'] = Review::EMAIL_META_KEY;
        $queryConfig['meta_value'] = $email;

        return ! $reviews->query( $queryConfig )->isEmpty();
    }

}