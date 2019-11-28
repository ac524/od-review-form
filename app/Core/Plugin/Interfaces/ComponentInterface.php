<?php
/**
 * Created by PhpStorm.
 * User: ac524
 * Date: 2/21/2019
 * Time: 1:07 PM
 */

namespace OdReviewForm\Core\Plugin\Interfaces;

use OdReviewForm\Core\Plugin\Components\Component;
use OdReviewForm\Core\Plugin\Plugin;

/**
 * Interface ComponentInterface
 * @package ComposerPress\Plugin\Interfaces
 * @see Component
 */
interface ComponentInterface
{

    /**
     * @return string The unique ID of the component
     */
    public function getId() : string;

    public function getPlugin() : Plugin;

    public function registerHooks() : self;

}