<?php
/**
 * Created by PhpStorm.
 * User: ac524
 * Date: 2/21/2019
 * Time: 1:16 PM
 */

namespace OdReviewForm\Core\Plugin\Components;


use OdReviewForm\Core\Plugin\Interfaces\ComponentInterface;
use OdReviewForm\Core\Plugin\Plugin;

abstract class Component implements ComponentInterface
{

    /** @var string Unique ID for the component instance. Should be defined in the child class instance. */
    protected $id;

    /** @var Plugin $plugin */
    protected $plugin;

    /** @var string|null Override default slug inherited from the parent plugin. */
    protected $slug;

    /**
     * Component constructor.
     * @param Plugin $plugin
     */
    public function __construct( Plugin $plugin )
    {

        $this->plugin = $plugin;

    }

    /**
     * @return string
     */
    public function getId(): string
    {

        return $this->id;

    }

    public function getPlugin(): Plugin
    {

        return $this->plugin;

    }

    public function getSlug() : string
    {

        return $this->slug ?? $this->getPlugin()->getSlug();

    }

}