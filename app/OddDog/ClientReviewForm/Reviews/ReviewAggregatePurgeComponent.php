<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Reviews;


use OdReviewForm\Core\Plugin\Components\Component;
use OdReviewForm\Core\Plugin\Interfaces\ComponentInterface;
use OdReviewForm\OddDog\ClientReviewForm\Plugin;

class ReviewAggregatePurgeComponent extends Component
{
    protected $id = 'OdReviewAggregatePurge';

    public function registerHooks(): ComponentInterface
    {
        add_action( 'transition_post_status', [ $this, 'manageCacheOnTransition' ], 10, 3 );

        return $this;
    }

    /**
     * @param string $oldStatus
     * @param string $newStatus
     * @param \WP_Post $post
     */
    public function manageCacheOnTransition( $oldStatus, $newStatus, $post )
    {
        if( ! $this->isReviewPostType( $post->post_type ) || $oldStatus === $newStatus )

            return;

        ReviewAggregate::clearAll();
    }

    private function isReviewPostType( string $type )
    {
        return $type === Plugin::instance()->getComponent( 'OdReviewsPostType' )->postType;
    }

}