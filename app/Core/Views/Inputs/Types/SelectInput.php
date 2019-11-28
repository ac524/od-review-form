<?php


namespace OdReviewForm\Core\Views\Inputs\Types;


use OdReviewForm\Core\Views\Inputs\Input;

class SelectInput extends Input
{

    /** @var array */
    protected $options;

    protected function inputHtml(): string
    {
        return sprintf(
            '<select name="%s"%s%s>%s</select>',
            $this->inputName(),
            $this->idAttr(),
            $this->disabledAttr(),
            $this->optionsHtml()
        );
    }

    private function optionsHtml(): string
    {
        return implode( '', array_map( [ $this, 'optionHtml' ], $this->options, array_keys( $this->options ) ) );
    }

    private function optionHtml( $label, $value )
    {
        return sprintf('<option value="%s"%s>%s</option>', $value, $this->selectedValueAttr( $value ), $label );
    }

    private function selectedValueAttr( $value ) : string
    {
        return $value === $this->value ? ' selected="selected"' : '';
    }

}