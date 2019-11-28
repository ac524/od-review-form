<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Views\Admin\ReviewMetabox;


use OdReviewForm\OddDog\ClientReviewForm\Views\Inputs\Inputs;
use OdReviewForm\OddDog\ClientReviewForm\Locations\Locations;
use OdReviewForm\OddDog\ClientReviewForm\Reviews\Review;
use OdReviewForm\OddDog\ClientReviewForm\Views\HtmlOutput;
use OdReviewForm\OddDog\ClientReviewForm\Views\RatingStars;

class ReviewMetabox extends HtmlOutput
{

    const REVIEW_FIELD_NAME = 'odreview';

    /** @var Locations */
    private $locations;

    /** @var Review */
    private $review;

    public function __construct( Review $review )
    {
        $this->locations = Locations::instance();

        $this->review = $review;
    }

    public function getHtml(): string
    {
        return sprintf(
            '<p><strong>Rating:</strong> %s</p>'.
            '<p><strong>Name:</strong> %s</p>'.
            '<p><strong>Email:</strong> %s</p>'.
            '%s'.
            '<p><strong>Review Message:</strong> %s</p>',
            new RatingStars( $this->review->rating ?: 0 ),
            $this->review->reviewer,
            $this->review->email,
            $this->locationSelectField(),
            $this->review->reviewMessage
        );
    }

    private function locationSelectField()
    {
        if( $this->locations->count() <= 1 )

            return '';

        $selectConfig = [
            'name' => self::REVIEW_FIELD_NAME. '.location',
            'value' => $this->review->hasLocation() ? $this->review->location : '',
            'inline' => true,
            'options' => [
                '' => 'No Location'
            ]
        ];

        foreach ( $this->locations->all() as $id => $location )

            $selectConfig['options'][$id] = $location->name;

        return sprintf('<div><strong>Location:</strong> %s</div>', Inputs::instance()->factory( 'select', $selectConfig ) );
    }

}