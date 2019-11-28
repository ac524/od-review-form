<?php
/**
 * Created by PhpStorm.
 * User: ac524
 * Date: 2/22/2019
 * Time: 1:24 PM
 */

namespace OdReviewForm\Core\Plugin\Components\WpAdminPage;

abstract class AdminToolsPageComponent extends AdminPageComponent
{

    public function addPage() : void
    {
        add_management_page(
            $this->getPageTitle(),
            $this->getMenuTitle(),
            $this->getRequiredPermission(),
            $this->getSlug(),
            [ $this, 'printPage' ]
        );
    }

    public function pageUrl(): string
    {
        return admin_url( 'tools.php?page='. $this->getSlug() );
    }

}