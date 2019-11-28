<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Reviews\Traits;

/**
 * Static caching trait for storing objects by a post id reference.
 *
 * Trait PostIdCache
 * @package ComposerPress\OddDog\ClientReviewForm\Reviews\Traits
 */
trait PostIdCache
{

    private static $postIdCache = [];

    protected static function addPostItem( int $postId, $item ) : void
    {
        if( ! self::hasPostItem( $postId ) )

            self::$postIdCache[ $postId ] = $item;
    }

    protected static function removePostItem( int $postId ) : void
    {
        if( self::hasPostItem( $postId ) )

            unset( self::$postIdCache[ $postId ] );
    }

    protected static function getPostItems() : array
    {
        return self::$postIdCache;
    }

    protected static function getPostItem( int $postId )
    {
        return self::$postIdCache[ $postId ] ?? null;
    }

    protected static function hasPostItem( int $postId ) : bool
    {
        return isset( self::$postIdCache[ $postId ] );
    }

    protected static function hasPostItems() : bool
    {
        return self::countPostItems() > 0;
    }

    protected static function countPostItems() : int
    {
        return count( self::$postIdCache );
    }

}