<?php

namespace OdReviewForm\OddDog\ClientReviewForm\Views\Widgets;

use OdReviewForm\Core\Plugin\Components\Component;
use OdReviewForm\Core\Plugin\Interfaces\ComponentInterface;

class ReviewsWidgetComponent extends Component
{

    protected $id = 'OdClientReviewsWidgetComponent';

    public function registerHooks(): ComponentInterface
    {
        add_action( 'widgets_init', [$this,'registerWidget'] );

        return $this;
    }

    public function registerWidget()
    {
        register_widget( ReviewsWidget::class );
    }
}