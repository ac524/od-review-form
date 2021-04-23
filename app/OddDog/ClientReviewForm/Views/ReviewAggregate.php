<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Views;


use OdReviewForm\Core\Interfaces\HtmlOutputInterface;
use OdReviewForm\Core\Traits\HtmlOutput;
use OdReviewForm\OddDog\ClientReviewForm\Account\AccountInfo;
use OdReviewForm\OddDog\ClientReviewForm\Reviews\ReviewAggregate as ReviewAggregateData;

class ReviewAggregate implements HtmlOutputInterface
{

    use HtmlOutput;

    /** @var ReviewAggregateData  */
    private $aggregate;

    /** @var string|null */
    private $businessName;

    public function __construct( ?string $location = null )
    {
        $this->businessName = AccountInfo::instance()->settings->businessName;

        $this->aggregate = empty( $location )

            ? ReviewAggregateData::getInstance()

            : ReviewAggregateData::getLocationInstance( $location );
    }

    public function getHtml(): string
    {
        if( ! $this->aggregate->hasData() )

            return '';

        $content = sprintf(
    '<div class="odrf-aggregate-wrap">
                %s
                <span class="odrf-aggregate-avg">%s</span>%s
                <br />
                <span class="odrf-aggregate-count">%s</span>
            </div>',
            $this->businessName ? '<span class="odrf-aggregate-name">'. $this->businessName .' Rating</span>' : "",
            number_format( $this->aggregate->average, 1 ),
            ( new RatingStars( $this->aggregate->average ) ),
            'Based on '. $this->aggregate->count .' Review'. ( 1 === $this->aggregate->count ? '' : 's' )
        );

        return $content;
    }

}