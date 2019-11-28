<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Views\Forms\Inputs;


use OdReviewForm\Core\Interfaces\HtmlOutputInterface;
use OdReviewForm\Core\Traits\HtmlOutput;

class RatingInput implements HtmlOutputInterface
{

    use HtmlOutput;

    public function getHtml(): string
    {
        return
            '<div class="odrf-stars-wrap"><div class="odrf-stars">'
                . implode( '', array_fill(0,5, $this->getSvgHtml()) )
                .'<input type="hidden" name="%s">'
            .'</div></div>';
    }

    private function getSvgHtml()
    {
        return '<button type="button" class="odrf-star"><svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" class="odrf-star-empty"><path d="M15.668 8.626l8.332 1.159-6.065 5.874 1.48 8.341-7.416-3.997-7.416 3.997 1.481-8.341-6.064-5.874 8.331-1.159 3.668-7.626 3.669 7.626zm-6.67.925l-6.818.948 4.963 4.807-1.212 6.825 6.068-3.271 6.069 3.271-1.212-6.826 4.964-4.806-6.819-.948-3.002-6.241-3.001 6.241z"/>
</svg><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="odrf-star-filled"><path d="M12 .587l3.668 7.568 8.332 1.151-6.064 5.828 1.48 8.279-7.416-3.967-7.417 3.967 1.481-8.279-6.064-5.828 8.332-1.151z"/></svg></button>';
    }

}