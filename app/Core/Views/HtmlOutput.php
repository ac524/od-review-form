<?php


namespace OdReviewForm\Core\Views;

use OdReviewForm\Core\Interfaces\HtmlOutputInterface;

abstract class HtmlOutput implements HtmlOutputInterface
{
    use \OdReviewForm\Core\Traits\HtmlOutput;
}