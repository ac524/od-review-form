<?php
/**
 * Created by PhpStorm.
 * User: ac524
 * Date: 2/26/2019
 * Time: 12:37 PM
 */

namespace OdReviewForm\Core\Traits;


use OdReviewForm\Core\Interfaces\HtmlOutputInterface;

/**
 * Default functionality for meeting HtmlOutputInterface requirements.
 *
 * Trait HtmlOutput
 * @package ComposerPress\Core\Traits
 */
trait HtmlOutput
{

    /**
     * @return string
     * @see HtmlOutputInterface::__toString()
     */
    public function __toString() : string
    {

        return $this->getHtml();

    }

    /**
     * @see HtmlOutputInterface::printHtml()
     */
    public function printHtml() : void
    {

        echo $this->getHtml();

    }

}