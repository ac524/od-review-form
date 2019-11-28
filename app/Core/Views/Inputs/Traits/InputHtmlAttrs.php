<?php


namespace OdReviewForm\Core\Views\Inputs\Traits;


trait InputHtmlAttrs
{

    protected function valueAttr() : string
    {
        return ' value="'. $this->value .'"';
    }

    protected function disabledAttr() : string
    {
        return $this->disabled ? ' disabled="disabled"' : '';
    }

    protected function idAttr() : string
    {
        return $this->id ? ' id="'. $this->id .'"' : '';
    }
}