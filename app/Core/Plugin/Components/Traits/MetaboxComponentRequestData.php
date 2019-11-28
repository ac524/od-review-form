<?php
/**
 * Created by PhpStorm.
 * User: ac524
 * Date: 2/21/2019
 * Time: 5:48 PM
 */

namespace OdReviewForm\Core\Plugin\Components\Traits;

use OdReviewForm\Core\Collections\MapClassCollection;
use OdReviewForm\Core\Exceptions\InvalidCollectionConfiguation;
use OdReviewForm\Core\Plugin\Exceptions\InvalidRequestDataConfiguration;
use OdReviewForm\Core\Plugin\Interfaces\RequestDataInterface;
use OdReviewForm\Core\Plugin\Requests\Request;
use OdReviewForm\Core\Plugin\Requests\Requests;

/**
 * Trait MetaboxComponentRequestData
 * @package ComposerPress\Core\Plugin\Components\Traits
 *
 * @method Requests getRequests()
 * @see ComponentRequestData::getRequests()
 *
 */
trait MetaboxComponentRequestData
{
    function allRequestInputsHtml() : string
    {
        return implode( '', array_map( [ $this, 'requestInputHtml'], $this->getRequests()->keys() ) );
    }

    function requestInputHtml(string $id ) : string
    {
        return '<input type="hidden" name="'. $id .'Submit" value="1" >';
    }

}