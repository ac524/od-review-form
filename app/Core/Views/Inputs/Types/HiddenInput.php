<?php


namespace OdReviewForm\Core\Views\Inputs\Types;


use OdReviewForm\Core\Views\Inputs\Input;

class HiddenInput extends Input
{
    protected function inputHtml(): string
    {
        return sprintf(
            '<input type="hidden" name="%s"%s%s%s>',
            $this->inputName(),
            $this->idAttr(),
            $this->valueAttr(),
            $this->disabledAttr()
        );
    }

}