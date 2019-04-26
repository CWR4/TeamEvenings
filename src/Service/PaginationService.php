<?php


namespace App\Service;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaginationService extends AbstractController
{
    private $page;
    private $totalPages;
    private const resultsPerPage = 10;

    public function __construct($page = 1, $totalResults = 1)
    {
        $this->page = $page;
        $this->totalPages = ceil($totalResults / self::resultsPerPage);
    }

    public function getTotalPages() : int
    {
        return $this->totalPages;
    }

    public function getPagination() : void
    {

    }
}