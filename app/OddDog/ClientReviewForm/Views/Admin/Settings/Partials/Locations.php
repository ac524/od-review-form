<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Views\Admin\Settings\Partials;


use OdReviewForm\OddDog\ClientReviewForm\Locations\Locations as LocationsData;
use OdReviewForm\OddDog\ClientReviewForm\Views\Admin\Settings\SettingsPage;

class Locations extends SettingPageHtmlOutput
{

    /** @var LocationsData */
    protected $locations;

    protected $locationsForm;

    protected $errors = [];

    public function __construct( SettingsPage $settingsPage)
    {
        parent::__construct($settingsPage);

        $this->locations = LocationsData::instance();

        $this->locationsForm = $this->settingsPage->component()->getForms()
            ->get( 'Locations' );

        if( $this->locations->failedLoad )

            $this->errors[] = 'We encountered an error trying to retrieve your locations.';
    }

    public function getHtml(): string
    {
        if( ! $this->hasErrors() && empty( $this->locations ) )

            return '';

        $html = '<div id="poststuff" class="postbox">';

        if( $this->locations && $this->locations->count() > 1 )

            $html .=
                '<h2>Locations</h2>'.
                '<div class="inside">'.
                    $this->lastSyncHtml( $this->locations->lastFetchTime() ).
                    $this->locationsForm->getHeaderHtml().
                    ( $this->hasErrors() ? $this->errorsHtml() : $this->locationsListHtml() ) .
                    $this->locationsForm->getFooterHtml().
                '</div>';

        else

            $html .=
                '<h2>Location Information</h2>'.
                '<div class="inside">'.
                    $this->lastSyncHtml( $this->locations->lastFetchTime() ).
                    $this->locationsForm->getHeaderHtml().
                    ( $this->hasErrors() ? $this->errorsHtml() : $this->singleLocationHtml() ) .
                    $this->locationsForm->getFooterHtml().
                '</div>';

        $html .= '</div>';

        return $html;
    }

    protected function lastSyncHtml( $time )
    {
        return sprintf(
            '<p>Locations Last Synced: %s &bull; <a href="%s" style="text-decoration: none;">Resync <span class="dashicons dashicons-backup"></span></a></p>',
            date( 'n/j/y', $time ),
            add_query_arg( 'resyncLocations', 1, $this->settingsPage->component()->pageUrl() )
        );
    }

    protected function errorsHtml() : string
    {
        if( empty( $this->errors ) )

            return '';

        return '<div class="alert alert-danger">'. implode( '', $this->errors ) .'</div>';
    }

    protected function singleLocationHtml() : string
    {
        if( ! $this->locations )

            return 'No Locations';

        $html = '<ul>';

        foreach ( $this->locations->all() as $location ) {

            $html .=
                '<li id="location-'. $location->id() .'">'.
                '<p><strong>OddDog Link Status:</strong> <span class="location-status"><span class="location-status-text">'. $location->status() .'</span><span class="spinner"></span></span></p>'.
                (new LocationFields( $location )).
                '</li>';

        }

        $html .= '<ul>';

        return $html;
    }

    protected function locationsListHtml() : string
    {
        if( ! $this->locations )

            return 'No Locations';

        $html = '<ul class="odrf-locations-list">';

        foreach ( $this->locations->all() as $location ) {

            $html .=
            '<li id="location-'. $location->id() .'">'.
                '<h3>'. $location->name .'</h3>'.
                '<p><strong>Reviews Shortcode:</strong> [OD_REVIEWS location="'. $location->id() .'"]</p>'.
                '<p><strong>Form Shortcode:</strong> [OD_REVIEW_FORM location="'. $location->id() .'"]</p>'.
                '<p><strong>Form Url:</strong> <a href="'. $location->url() .'">'. $location->url() .'</a></p>'.
                '<p><strong>OddDog Link Status:</strong> <span class="location-status"><span class="location-status-text">'. $location->status() .'</span><span class="spinner"></span></span></p>'.
                (new LocationFields( $location )).
            '</li>';

        }

        $html .= '<ul>';

        return $html;
    }

    protected function hasErrors()
    {
        return ! empty( $this->errors );
    }

}