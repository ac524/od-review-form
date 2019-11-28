<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Admin;

use OdReviewForm\Core\Plugin\Components\MetaboxComponent;
use OdReviewForm\Core\Plugin\Components\Traits\ComponentEnqueues;
use OdReviewForm\Core\Plugin\Components\Traits\ComponentRequestData;
use OdReviewForm\Core\Plugin\Components\Traits\MetaboxComponentRequestData;
use OdReviewForm\Core\Plugin\Interfaces\EnqueueResourcesInterface;
use OdReviewForm\Core\Plugin\Interfaces\RequestDataInterface;
use OdReviewForm\OddDog\ClientReviewForm\Reviews\Review;
use OdReviewForm\OddDog\ClientReviewForm\Views\Admin\ReviewMetabox\ReviewMetabox as ReviewMetaboxView;
use OdReviewForm\OddDog\ClientReviewForm\Views\Admin\ReviewMetabox\ReviewMetabox;

class ReviewMetaboxComponent extends MetaboxComponent implements EnqueueResourcesInterface, RequestDataInterface
{

    use ComponentEnqueues;
    use ComponentRequestData;
    use MetaboxComponentRequestData;

    /** @var string  */
    protected $id = 'OdReviewMetabox';

    /** @var string  */
    protected $metaboxId = 'odreviewaccount';

    /** @var string  */
    protected $title = 'Review Information';

    /** @var string  */
    protected $screen = 'odreview';

    /** @var array|null */
    protected $requestData;

    /** @var Review */
    private $review;

    protected function getStyles(): array
    {
        return [
            [ 'odrf-admin', $this->getCssFileUrl(  'admin.css' ) ]
        ];
    }

    public function registerRequests(): void
    {
        $this->addRequest( 'reviewUpdate', INPUT_POST, [
            ReviewMetabox::REVIEW_FIELD_NAME => [
                'filter' => FILTER_DEFAULT,
                'flags' => FILTER_REQUIRE_ARRAY
            ]
        ] );
    }

    public function processRequestData(): void
    {
        $activeRequest = $this->getActiveRequest();

        $this->requestData = $activeRequest->getRequestData();

        if( ! empty( $this->requestData[ ReviewMetabox::REVIEW_FIELD_NAME ] ) )

            $this
                ->review()
                ->update( $this->requestData[ ReviewMetabox::REVIEW_FIELD_NAME ] )
                ->save();
    }

    public function printContent(): void
    {
        echo
            (new ReviewMetaboxView( $this->review() )) .
            $this->allRequestInputsHtml();
    }

    protected function review() : Review
    {
        if( null === $this->review )

            $this->review = Review::postFactory( $GLOBALS['post'] );

        return $this->review;
    }

}