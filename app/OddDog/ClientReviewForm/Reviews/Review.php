<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Reviews;

use OdReviewForm\Core\Collections\CollectionClass;
use OdReviewForm\Core\Plugin\Interfaces\ComponentInterface;
use OdReviewForm\Core\Traits\ObjectProperties;
use OdReviewForm\OddDog\ClientReviewForm\Locations\Location;
use OdReviewForm\OddDog\ClientReviewForm\Locations\Locations;
use OdReviewForm\OddDog\ClientReviewForm\Plugin;
use OdReviewForm\OddDog\ClientReviewForm\Reviews\Traits\PostIdCache;

class Review extends CollectionClass
{

    use PostIdCache;

    const LOCATION_META_KEY = '_odreview_location';
    const EMAIL_META_KEY = '_odreviewer_email';
    const RATING_META_KEY = '_odreview_rating';

    public $id;

    public $reviewer;

    public $email;

    public $rating;

    public $date;

    public $reviewMessage;

    public $location;

    /** @var Location */
    private $locationItem;

    /** @var \WP_Post */
    private $post;

    private static $updatable = [ 'location' ];

    private static $cache = [];

    public static function postIdFactory( int $postId ) : self
    {
        return self::getPostItem( $postId ) ?? self::postFactory( get_post( $postId ) );
    }

    public static function postFactory( \WP_Post $post ) : self
    {
        if( self::hasPostItem( $post->ID ) )

            return self::getPostItem( $post->ID );

        $review = new self;

        $review->id = $post->ID;
        $review->post = $post;

        $review->reviewer = $post->post_title;
        $review->reviewMessage = $post->post_content;
        $review->date = $post->post_date;

        $review->rating = (int)get_post_meta( $review->id, self::RATING_META_KEY, true );
        $review->email = get_post_meta( $review->id, self::EMAIL_META_KEY, true );
        $review->location = get_post_meta( $review->id, self::LOCATION_META_KEY, true );

        self::addPostItem( $post->ID, $review );

        return $review;
    }

    public function __construct( ?array $options = null )
    {
        if( ! empty( $options ) )

            $this->updateProperties( $options );
    }

    public function update( array $options ) : self
    {
        $optionsToUpdate = array_intersect_key( $options, array_flip( self::$updatable ) );

        $this->updateProperties( $optionsToUpdate );

        return $this;
    }

    public function save() : self
    {
        return $this->isCreated()

            ? $this->saveUpdate()

            : $this->saveCreate();
    }

    public function isCreated()
    {
        return ! empty( $this->id );
    }

    public function post()
    {
        return $this->post;
    }

    public function hasLocation() : bool
    {
        return ! empty( $this->location ) && Locations::instance()->containsKey( $this->location );
    }

    public function locationName() : ?string
    {
        return $this->hasLocation() ?  Locations::instance()->get( $this->location )->name : null;
    }

    public function locationItem() : ?Location
    {
        if( ! empty( $this->locationItem ) )

            return $this->locationItem;

        if( empty( $this->location ) )

            return null;

        $this->locationItem = Locations::instance()->get( $this->location );

        return $this->locationItem;
    }

    private function postData() : array
    {
        return [
            'post_type' => $this->postType()->postType,
            'post_title' => $this->reviewer,
            'post_content' => $this->reviewMessage,
            'meta_input' => [
                self::EMAIL_META_KEY => $this->email,
                self::RATING_META_KEY => $this->rating,
                self::LOCATION_META_KEY => $this->location
            ]
        ];
    }

    private function saveCreate() : self
    {
        $this->id = wp_insert_post( $this->postData() );

        $this->post = get_post( $this->id );

        $this->date = $this->post->post_date;

        wp_publish_post( $this->post );

        return $this;
    }

    private function saveUpdate() : self
    {
        update_post_meta( $this->id, self::LOCATION_META_KEY, $this->location );

        return $this;
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