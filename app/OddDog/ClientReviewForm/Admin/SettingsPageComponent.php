<?php

namespace OdReviewForm\OddDog\ClientReviewForm\Admin;

use OdReviewForm\Core\Plugin\Components\Traits\ComponentEnqueues;
use OdReviewForm\Core\Plugin\Components\Traits\ComponentRequestData;
use OdReviewForm\Core\Plugin\Components\Traits\ComponentRequestDataForms;
use OdReviewForm\Core\Plugin\Components\WpAdminPage\AdminPostTypePageComponent;
use OdReviewForm\Core\Plugin\Exceptions\InvalidComponentConfiguration;
use OdReviewForm\Core\Plugin\Interfaces\ComponentInterface;
use OdReviewForm\Core\Plugin\Interfaces\EnqueueResourcesInterface;
use OdReviewForm\Core\Plugin\Interfaces\RequestDataInterface;
use OdReviewForm\OddDog\ClientReviewForm\FormPage\FormPage;
use OdReviewForm\OddDog\ClientReviewForm\Locations\Locations;
use OdReviewForm\OddDog\ClientReviewForm\Reviews\OdReviewsPostType;
use OdReviewForm\OddDog\ClientReviewForm\Settings;
use OdReviewForm\OddDog\ClientReviewForm\Views\Admin\Settings\Partials\Notice;
use OdReviewForm\OddDog\ClientReviewForm\Views\Admin\Settings\SettingsPage;
use OdReviewForm\OddDog\FormsApiClient\ReviewFormsClient;

class SettingsPageComponent extends  AdminPostTypePageComponent implements RequestDataInterface, EnqueueResourcesInterface {

    use ComponentRequestData;
    use ComponentRequestDataForms;
    use ComponentEnqueues;

    protected $pageTitle = 'Settings';

    protected $pageStatus;

    protected $pageErrorCodes = [];

    protected $pageMessage;

    protected $postType = OdReviewsPostType::POST_TYPE;

	protected $id = 'OdClientReviewFormSettings';

	public function addPage(): void
    {

        if( Settings::getInstance()->isCodeValidated() )

            parent::addPage();

        else

            add_menu_page(
                $this->getPageTitle(),
                $this->getMenuTitle(),
                $this->getRequiredPermission(),
                $this->getSlug(),
                [ $this, 'printPage' ],
                $this->postTypeComponent()->menuIcon,
                $this->postTypeComponent()->menuPosition
            );

    }

    public function pageUrl(): string
    {
        if( Settings::getInstance()->isCodeValidated() )

            return parent::pageUrl();

        else

            return admin_url( 'admin.php?page='. $this->getSlug() );
    }

    public function getMenuTitle(): string
    {
        if( Settings::getInstance()->isCodeValidated() )

            return parent::getMenuTitle();

        else

            return $this->postTypeComponent()->label;
    }

    /**
     * @throws InvalidComponentConfiguration
     */
    public function registerRequests(): void
    {
        $settings = Settings::getInstance();

        // TODO Move to better location?
        if( ! empty( $_GET['resyncLocations'] ) ) {

            if( $settings->isCodeValidated() ) {

                Locations::instance()->fetch();

                add_action('admin_init', function() {

                    wp_redirect( $this->pageUrl() );

                });

            }

        }

        $this
            ->addForm( 'GeneralSettings', INPUT_POST, [
                'accountCode' => [
                    'label' => 'OddDog Reviews Account Code',
                    'type' => 'text',
                    'disabled' => true
                ],
                'accountToken' => [
                    'label' => 'OddDog Reviews API Token',
                    'type' => 'text',
                    'disabled' => true
                ],
                'businessName' => [
                    'label' => 'Business Name',
                    'type' => 'text'
                ],
                'submit' => 'Update Account Information',
                'nonce' => true
            ] )
            ->addForm( 'Locations', INPUT_POST, [
                'locations' => [
                    'filter' => [
                        'filter' => FILTER_DEFAULT,
                        'flags' => FILTER_REQUIRE_ARRAY
                    ],
                    'type' => 'custom'
                ],
                'submit' => 'Update Location Information',
                'nonce' => true
            ] );

    }

    protected function getStyles(): array
    {
        return [
            [ 'odrf-admin', $this->getCssFileUrl(  'admin.css' ) ]
        ];
    }

    protected function getScripts(): array
    {
        return [
            [ 'odrf-admin', $this->getJsFileUrl(  'admin.js' ), [ 'jquery' ], 1.0, true ]
        ];
    }

    protected function getLocalizedScriptsVars(): array
    {
        $isValidated = Settings::getInstance()->isCodeValidated();

        $data = [
            'apiUrl' => home_url( '/wp-json/odrfadmin/v1/' ),
            'locations' =>  $isValidated ? Locations::instance()->all() : null,
            'nonce' => wp_create_nonce( 'wp_rest' ),
        ];

        return [
            [
                'odrf-admin', 'odrfAdminOptions', $data
            ]
        ];
    }

    public function printPage(): void
    {
        $settings = Settings::getInstance();

        if( $settings->isCodeValidated() ) {

            $locations = Locations::instance();

            if( $locations->isOutOfDate() ) {

                $locations->fetch();

            }

        }

        echo new SettingsPage( $this );
	}

	public function processRequestData(): void
    {
        $requestKey = $this->getActiveRequestKey();

        if( empty( $requestKey ) )

            return;

        $submissionForm = $this->getForms()->get( $requestKey );

        if( !empty( $submissionForm ) ) {

            if( $submissionForm->hasNonce() && !$submissionForm->isNonceValid( $this->getRequestDataValue( $submissionForm->getNonceName() ) ) ) {

                $this->processingError = 'Unable to validate form submission.';

                return;

            }

        }

        static $requestActionMap = [
            'GeneralSettings' => 'updateGeneralSettings',
            'Locations' => 'updateLocations'
        ];

        if( empty( $requestActionMap[ $requestKey ] ) )

            return;

        $this->{ $requestActionMap[ $requestKey ] }( $this->getRequestData() );

        if( ! empty( $this->pageStatus ) )

            add_action( 'admin_notices', [ $this, 'printStatus' ] );

    }

    public function updateGeneralSettings( array $options ) : void
    {
        $settings = Settings::getInstance();

        $prevAccountCode = $settings->accountCode;
        $validateToken = null;

        if( isset( $options['accountToken'] ) ) {

            $validateToken = $options['accountToken'];
            unset( $options['accountToken'] );

        }

        $settings
            ->updateProperties( $options );

        if( $settings->accountCode !== $prevAccountCode || ! $settings->isCodeValidated() ) {

            $this->processAccountCodeUpdate( $validateToken );

        }


        if( $settings->isCodeValidated() && empty( $settings->formPageId ) )

            $settings->formPageId = FormPage::instance()->createPage()->pageId();

        $settings->save();

        if( empty( $prevAccountCode ) && $settings->isCodeValidated() ) {

            wp_redirect( $this->pageUrl() );

        }

        if( empty( $this->pageStatus ) ) {

            $this->pageStatus = 'success';
            $this->pageMessage = 'Settings Updated';

        }
    }

    public function processAccountCodeUpdate( ?string $tokenCheck = null )
    {
        $settings = Settings::getInstance();

        $settings->accountToken = '';

        if( $settings->accountCode ) {

            $requestData = [];

            if( $tokenCheck )

                $requestData['token'] = $tokenCheck;

            $validationRequest = ReviewFormsClient::instance( $settings->accountCode )->validate( $requestData );

            if( ! $validationRequest->isHealthy() ) {

                $this->pageStatus = 'error';
                $this->pageErrorCodes = $validationRequest->getErrorCodes();
                $this->pageMessage = implode( ',', $validationRequest->getErrorMessages() );
                return;

            }

            $settings->accountToken = $validationRequest->result()['token'];

            // Fetch Location Data
            $locationsData = Locations::instance();

            if( $locationsData->isFromCache() )

                $locationsData->fetch();
        }
    }



    public function updateLocations( array $options )
    {
        $locations = Locations::instance();

        foreach ( $options[ 'locations' ] as $key => $locationDetails )

            $locations->get( $key )->update( $locationDetails );

        $updated = $locations->updated();

        if( ! $updated->isEmpty() ) {

            $locations->save();

            $settings = Settings::getInstance();

            $request =
                ReviewFormsClient::instance( $settings->accountCode, $settings->accountToken )
                    ->updateLocationDetails( $locations->all() );
        }


        $this->pageStatus = 'success';
        $this->pageMessage = 'Locations Updated';
    }

    public function printStatus()
    {
        echo new Notice( $this->pageMessage, $this->pageStatus );
    }

    public function hasPageStatus() : bool
    {
        return ! empty( $this->pageStatus );
    }

    public function getPageErrorCodes() : array
    {
        return $this->pageErrorCodes;
    }

    /**
     * @return ComponentInterface|OdReviewsPostType
     */
    private function postTypeComponent() : OdReviewsPostType
    {
        return $this->plugin->getComponent( OdReviewsPostType::COMPONENT_ID );
    }

}