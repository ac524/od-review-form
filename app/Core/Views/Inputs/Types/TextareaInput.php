<?php

namespace OdReviewForm\Core\Views\Inputs\Types;

use OdReviewForm\Core\Views\Inputs\Input;

class TextareaInput extends Input
{
    protected function inputHtml(): string
    {
        return sprintf(
            '<textarea type="text" name="%s"%s>%s</textarea>',
            $this->inputName(),
            $this->disabledAttr(),
            $this->value
        );
    }

}