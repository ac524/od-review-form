<?php


namespace OdReviewForm\OddDog\ClientReviewForm\FormPage;


use OdReviewForm\OddDog\ClientReviewForm\Settings;

class FormPage
{
    private static $instance;

    /**
     * @return FormPage
     */
    public static function instance() : self
    {
        if( null === self::$instance )

            self::$instance = new self;

        return self::$instance;
    }

    private function __construct()
    {
    }

    public function isPageCreated() : bool
    {
        return ! empty( $this->pageId() );
    }

    public function createPage() : self
    {
        global $wpdb;

        $pageId = (int)$wpdb->get_var( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'page' AND post_content LIKE '%[OD_REVIEW_FORM]%'" );

        if( ! $pageId ) {

            $postInfo = [
                'post_type' => 'page',
                'post_title' => 'Write a Review',
                'post_name' => 'write-a-review',
                'post_content' => '[OD_REVIEW_FORM]'
            ];

            $pageId = wp_insert_post( $postInfo );

        }

        if( ! is_wp_error( $pageId ) ) {

            Settings::getInstance()->formPageId = $pageId;
            wp_publish_post( $pageId );

        }

        return $this;
    }

    public function pageId() : ?int
    {
        return Settings::getInstance()->formPageId;
    }

    public function pageUrl() : ?string
    {
        $id = $this->pageId();

        if( empty( $id ) )

            return null;

        return get_permalink( $id ) ?: null;
    }
}