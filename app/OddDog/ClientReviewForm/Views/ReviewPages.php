<?php


namespace OdReviewForm\OddDog\ClientReviewForm\Views;


class ReviewPages extends HtmlOutput
{

    private $count;

    private $page;

    private $perPage;

    public function __construct( $count, $page, $perPage )
    {
        $this->count = $count;

        $this->page = $page;

        $this->perPage = $perPage;
    }

    public function getHtml(): string
    {
        $pagesConfig = [
            'total' => $this->totalPages(),
            'current' => $this->page,
            'prev_next' => false
        ];

        return
            '<div class="odrf-pages">'.
                (paginate_links( $pagesConfig ) ?? '').
            '</div>';
    }

    public function totalPages() : int
    {
        return ceil( $this->count / $this->perPage );
    }
}