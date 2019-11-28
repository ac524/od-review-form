<?php


namespace OdReviewForm\Core\Plugin\Components\Traits;


use OdReviewForm\Core\Plugin\Interfaces\CustomColumnsInterface;

/**
 * Trait PostTypeComponentCustomColumns
 * @package ComposerPress\Core\Plugin\Components\Traits
 */
trait PostTypeComponentCustomColumns
{

    protected $dashboardColumns;

    /**
     * @see CustomColumnsInterface::registerCustomColumns()
     */
    public function registerCustomColumns() : void
    {
        if( ! is_admin() )

            return;

        $this->dashboardColumns = [];

        $this->addDashboardColumns();

        add_filter( 'manage_'. $this->postType .'_posts_columns', [ $this, 'filterPostTypeColumns' ] );

        add_action( 'manage_'. $this->postType .'_posts_custom_column', [ $this, 'printCustomColumns' ], 10, 2 );
    }

    public abstract function addDashboardColumns();

    public function filterPostTypeColumns( $columns )
    {
        if( empty( $this->dashboardColumns ) )

            return $columns;

        return $this->dashboardColumns;

    }

    public function printCustomColumns( $column, $post_id )
    {
        $callable = [ $this, 'print'. ucfirst( $column ) .'CustomColumn' ];

        if( is_callable( $callable ) )

            call_user_func( $callable, $post_id );
    }

}