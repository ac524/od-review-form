<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Reviews;

use OdReviewForm\Core\Collections\MapClassCollection;
use OdReviewForm\Core\Plugin\Interfaces\ComponentInterface;
use OdReviewForm\OddDog\ClientReviewForm\Plugin;
use WP_Query;

class Reviews extends MapClassCollection
{

    private $lastQuery;

    public function newQueryConfig() : array
    {
        return [
            'post_type' => $this->postType()->postType,
            'post_status' => 'publish',
            'posts_per_page' => 10,
            'paged' => get_query_var( 'paged', 1 )
        ];
    }

    public function query( ?array $queryConfig = null ) : self
    {

        if( empty( $queryConfig  ) )

            $queryConfig = $this->newQueryConfig();

        $this->lastQuery = $query = new WP_Query($queryConfig);

        /** @var \WP_Post $post */
        foreach ( $query->get_posts() as $post ) {

            $this->set( $post->ID, Review::postFactory( $post ) );

        }

        return $this;
    }

    public function lastQuery() : ?WP_Query
    {
        return $this->lastQuery;
    }

    /**
     * @return OdReviewsPostType|ComponentInterface
     */
    private function postType() : OdReviewsPostType
    {
        return $this->plugin()->getComponent( 'OdReviewsPostType' );
    }

    private function plugin() : Plugin
    {
        return Plugin::instance();
    }

}