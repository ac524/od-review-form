<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Rest;

use OdReviewForm\Core\Plugin\Components\RestRouteComponent;

class AdminActionsRoutesComponent extends RestRouteComponent {

	protected $id = 'odClientFormApiRestRoutes';

	protected $controllers = [
        LocationLinksController::class
	];

}