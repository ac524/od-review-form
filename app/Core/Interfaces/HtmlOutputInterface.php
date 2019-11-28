<?php
/**
 * Created by PhpStorm.
 * User: ac524
 * Date: 2/26/2019
 * Time: 12:38 PM
 */

namespace OdReviewForm\Core\Interfaces;

interface HtmlOutputInterface
{

    /**
     * String conversion magic method for easy inclusion in strings.
     * @return string
     */
    public function __toString() : string;

    /**
     * Return the HTML output as a string.
     *
     * @return string
     */
    public function getHtml() : string;

    /**
     * Add HTML output to the output buffer.
     */
    public function printHtml() : void;

}