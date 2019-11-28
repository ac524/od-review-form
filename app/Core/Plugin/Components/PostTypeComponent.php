<?php


namespace OdReviewForm\Core\Plugin\Components;


use OdReviewForm\Core\Plugin\Components\Traits\PostTypeComponentCustomColumns;
use OdReviewForm\Core\Plugin\Interfaces\ComponentInterface;
use OdReviewForm\Core\Plugin\Interfaces\CustomColumnsInterface;
use OdReviewForm\Core\Traits\ImplementsInterfaces;
use OdReviewForm\Core\Traits\ObjectProperties;

class PostTypeComponent extends Component
{

    use ObjectProperties;
    use ImplementsInterfaces;

    public $postType;

    public $label;

    public $labels;

    public $description;

    public $public;

    public $hierarchical;

    public $excludeFromSearch;

    public $publiclyQueryable;

    public $showUi;

    public $showInMenu;

    public $showInNavMenus;

    public $showInAdminBar;

    public $rewrite = false;

    public $capabilityType;

    public $capabilities;

    public $mapMetaCap;

    public $supports;

    public $menuIcon;

    public $menuPosition;

//    public $hasMetaBoxes = false;

    private static $args = [
        'label',
        'labels',
        'description',
        'public',
        'hierarchical',
        'exclude_from_search',
        'publicly_queryable',
        'show_ui',
        'show_in_menu',
        'show_in_nav_menus',
        'show_in_admin_bar',
        'show_in_rest',
        'rest_base',
        'rest_controller_class',
        'menu_position',
        'menu_icon',
        'capability_type',
        'capabilities',
        'map_meta_cap',
        'supports',
        'register_meta_box_cb',
        'taxonomies',
        'has_archive',
        'rewrite',
        'query_var',
        'can_export',
        'delete_with_user'
    ];

    public function registerHooks(): ComponentInterface
    {
        register_post_type( $this->postType, $this->config() );

        return $this;
    }

//    public function registerMetaBoxes() : void
//    {
//
//    }

    protected function config() : array
    {

        $properties = $this->getPublicProperties();

        $config = [];

        foreach ( $properties as $propertyName ) {

            if( null === $this->{ $propertyName } )

                continue;

            $optionName = $this->getOptionName( $propertyName );

            if( empty( $optionName ) )

                continue;

            $config[ $optionName ] = $this->{ $propertyName };

        }

//        if( $this->hasMetaBoxes )
//
//            $config[ 'register_meta_box_cb' ] = [ $this, 'registerMetaBoxes' ];

        return $config;

    }

    protected function getOptionName( string $propertyName )
    {
        $name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $propertyName));

        return in_array( $name, self::$args ) ? $name : null;
    }

}