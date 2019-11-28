<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Views\Admin\Settings\Partials;


use OdReviewForm\OddDog\ClientReviewForm\Locations\Location;
use OdReviewForm\OddDog\ClientReviewForm\Views\HtmlOutput;

class LocationFields extends HtmlOutput
{

    /** @var Location */
    private $location;

    public function __construct( Location  $location )
    {
        $this->location = $location;

        if( null === $this->location->address )

            $this->location->address = [];
    }

    public function getHtml(): string
    {
        return
            '<div class="form-field-group">'.
                $this->addressName().
                $this->addressField().
                $this->address2Field().
                $this->cityField().
                $this->stateField().
                $this->postalField().
                $this->countryField().
            '</div>';
    }

    protected function addressValue( $name ) : string
    {
        return $this->location->address[ $name ] ?? '';
    }

    protected function addressName()
    {
        $inputName = 'name';
        $inputId = $inputName .'-%s';

        return sprintf(
            '<div class="form-field">'.
                '<label for="%s">Location Business Name</label>'.
                '<input type="text" id="%s" name="locations[%s][address][%s]" value="%s">'.
            '</div>',
            $inputId,
            $inputId,
            $this->location->id(),
            $inputName,
            $this->addressValue($inputName)
        );
    }

    protected function addressField()
    {
        $inputName = 'streetAddress';
        $inputId = $inputName .'-%s';

        return sprintf(
            '<div class="form-field">'.
                '<label for="%s">Street Address</label>'.
                '<input type="text" id="%s" name="locations[%s][address][%s]" value="%s">'.
            '</div>',
            $inputId,
            $inputId,
            $this->location->id(),
            $inputName,
            $this->addressValue($inputName)
        );
    }

    protected function address2Field()
    {
        $inputName = 'address2';
        $inputId = $inputName .'-%s';

        return sprintf(
            '<div class="form-field">'.
            '<label for="%s">Address Line 2</label>'.
            '<input type="text" id="%s" name="locations[%s][address][%s]" value="%s">'.
            '</div>',
            $inputId,
            $inputId,
            $this->location->id(),
            $inputName,
            $this->addressValue($inputName)
        );
    }

    protected function cityField()
    {
        $inputName = 'city';
        $inputId = $inputName .'-%s';

        return sprintf(
            '<div class="form-field form-field-left-half">'.
            '<label for="%s">City</label>'.
            '<input type="text" id="%s" name="locations[%s][address][%s]" value="%s">'.
            '</div>',
            $inputId,
            $inputId,
            $this->location->id(),
            $inputName,
            $this->addressValue($inputName)
        );
    }

    protected function stateField()
    {
        $inputName = 'state';
        $inputId = $inputName .'-%s';

        return sprintf(
            '<div class="form-field form-field-right-half">'.
            '<label for="%s">State / Region</label>'.
            '<input type="text" id="%s" name="locations[%s][address][%s]" value="%s">'.
            '</div>',
            $inputId,
            $inputId,
            $this->location->id(),
            $inputName,
            $this->addressValue($inputName)
        );
    }

    protected function postalField()
    {
        $inputName = 'postal';
        $inputId = $inputName .'-%s';

        return sprintf(
            '<div class="form-field form-field-left-half">'.
            '<label for="%s">Postal Code</label>'.
            '<input type="text" id="%s" name="locations[%s][address][%s]" value="%s">'.
            '</div>',
            $inputId,
            $inputId,
            $this->location->id(),
            $inputName,
            $this->addressValue($inputName)
        );
    }

    protected function countryField()
    {
        $inputName = 'country';
        $inputId = $inputName .'-%s';

        return sprintf(
            '<div class="form-field form-field-right-half">'.
            '<label for="%s">Country</label>'.
            '<input type="text" id="%s" name="locations[%s][address][%s]" value="%s">'.
            '</div>',
            $inputId,
            $inputId,
            $this->location->id(),
            $inputName,
            $this->addressValue($inputName)
        );
    }

}