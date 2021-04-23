<?php

namespace OdReviewForm\OddDog\ClientReviewForm\Views\Widgets;

use OdReviewForm\OddDog\ClientReviewForm\Locations\Locations as LocationsData;
use OdReviewForm\OddDog\ClientReviewForm\Views\ReviewsDisplay;

class ReviewsWidget extends \WP_Widget
{

    function __construct() {
        parent::__construct(

            // Base ID of your widget
            'odrf_widget',

            // Widget name will appear in UI
            __('OddDog Reviews', 'odddog-review-form'),

            // Widget description
            array( 'description' => __( 'Display the reviews you\'ve collected', 'odddog-review-form' ), )
        );
    }

    // Creating widget front-end
    public function widget( $args, $instance ) {

        ReviewsDisplay::enqueueResources();

        // before and after widget arguments are defined by themes
        echo $args['before_widget'];

        echo new ReviewsDisplay( $instance );

        echo $args['after_widget'];

    }

    // Widget Backend
    public function form( $instance ) {

        if( !isset($instance[ 'style' ]) ) $instance[ 'style' ] = 'default';
        if( !isset($instance[ 'location' ]) ) $instance[ 'location' ] = '';
        if( !isset($instance[ 'aggregate' ]) ) $instance[ 'aggregate' ] = 1;
        if( !isset($instance[ 'reviews' ]) ) $instance[ 'reviews' ] = 10;
        if( !isset($instance[ 'pagination' ]) ) $instance[ 'pagination' ] = 1;

        $styleId = $this->get_field_id('style');
        ?>
        <p>
            <label for="<?= $styleId; ?>"><?php _e( 'Reviews Style:', 'odddog-review-form' ); ?></label>
            <select class="widefat" id="<?= $styleId; ?>" name="<?= $this->get_field_name( 'style' ); ?>" type="text">
                <?php
                foreach( ReviewsDisplay::$styleOptions as $option ) {
                    $selected = $option === $instance[ 'style' ] ? ' selected' : "";
                    echo "<option value=\"{$option}\"{$selected}>{$option}</option>";
                }
                ?>
            </select>
        </p>
        <?php

        $locationId = $this->get_field_id('location');
        $locations = LocationsData::instance();
        ?>
        <p>
            <label for="<?= $locationId; ?>"><?php _e( 'Location:', 'odddog-review-form' ); ?></label>
            <select class="widefat" id="<?= $locationId; ?>" name="<?= $this->get_field_name( 'location' ); ?>" type="text">
                <option value="">All</option>
                <?php
                foreach ( $locations->all() as $location ) {
                    $selected = $location->id() === $instance[ 'location' ] ? ' selected' : "";
                    echo "<option value=\"{$location->id()}\"{$selected}>{$location->name}</option>";
                }
                ?>
            </select>
        </p>
        <?php

        $reviewsCountId = $this->get_field_id('reviews');
        ?>
        <p>
            <label for="<?= $reviewsCountId; ?>"><?php _e( 'Reviews per page:', 'odddog-review-form' ); ?></label>
            <input class="tiny-text" id="<?= $reviewsCountId; ?>" name="<?= $this->get_field_name( 'reviews' ); ?>" type="number" step="1" min="0" value="<?= $instance['reviews'] ?>" size="3">
        </p>
        <?php

        $aggregateId = $this->get_field_id('aggregate');
        ?>
        <p>
            <input class="checkbox" type="checkbox" id="<?= $aggregateId; ?>" name="<?= $this->get_field_name( 'aggregate' ); ?>" <?= $instance[ 'aggregate' ] ? 'checked="checked"' : ""?> value="1">
            <label for="<?= $aggregateId; ?>">Display Aggregate Header?</label>
        </p>
        <?php

        $paginationId = $this->get_field_id('pagination');
        ?>
        <p>
            <input class="checkbox" type="checkbox" id="<?= $paginationId; ?>" name="<?= $this->get_field_name( 'pagination' ); ?>" <?= $instance[ 'pagination' ] ? 'checked="checked"' : ""?> value="1">
            <label for="<?= $paginationId; ?>">Display Pagination?</label>
        </p>
        <?php
    }

    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {

        return [
            'style' => $new_instance['style'],
            'location' => $new_instance['location'],
            'aggregate' => (int)$new_instance['aggregate'],
            'reviews' => (int)$new_instance['reviews'],
            'pagination' => (int)$new_instance['pagination']
        ];

    }

}