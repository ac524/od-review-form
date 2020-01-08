<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Views\Admin\Settings\Partials;

use OdReviewForm\OddDog\ClientReviewForm\Views\Admin\Settings\SettingsPage;
use OdReviewForm\Core\Plugin\Html\Forms\Form;

class NewAccountSetup extends SettingPageHtmlOutput
{

    /** @var Form  */
    private $settingsForm;

    public function __construct(SettingsPage $settingsPage)
    {
        parent::__construct($settingsPage);

        $this->settingsForm = $this->settingsPage->component()->getForms()
            ->get( 'GeneralSettings' )
            ->setInputValues( $this->settingsPage->settings() )
            ->setSubmitText( 'Connect My Odd Dog Reviews Account' );

        $setupInputs = [
            'accountCode'
        ];

        if( count( array_intersect( [ 'token-exists', 'token-invalid' ], $this->settingsPage->component()->getPageErrorCodes() ) ) )

            $setupInputs[] = 'accountToken';


        foreach ( $this->settingsForm->getInputNames() as $inputName ) {

            if( ! in_array( $inputName, $setupInputs ) )

                $this->settingsForm->removeInput( $inputName );

            else

                $this->settingsForm->updateInputConfig( $inputName, [ 'disabled' => false ] );

        }
    }

    public function getHtml(): string
    {
        return
            '<div class="odrf-intro">' .
                '<h3>Welcome To Odd Dog Website Reviews!</h3>' .
                '<p>Please enter your Odd Dog Reviews account code below to<br>get started with collecting reviews on your website.</p>' .
                $this->settingsForm .
                '<p>Don\'t have an account code yet? <a href="https://odd.dog/review-app/">Register For Free!</a></p>' .
            '</div>';
    }

}