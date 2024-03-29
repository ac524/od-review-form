<?php

namespace OdReviewForm\Core\Plugin\Components\WpAdminPage;


use OdReviewForm\Core\Plugin\Interfaces\RequestDataInterface;

abstract class AdminPostTypePageComponent extends AdminPageComponent implements RequestDataInterface
{

    protected $postType;

    public function addPage(): void
    {

        add_submenu_page(
            'edit.php?post_type='. $this->postType,
            $this->getPageTitle(),
            $this->getMenuTitle(),
            $this->getRequiredPermission(),
            $this->getSlug(),
            [ $this, 'printPage' ]
        );


    }

    public function pageUrl() : string
    {
        return admin_url( 'edit.php?post_type='. $this->postType. '&page='. $this->getSlug() );
    }

}