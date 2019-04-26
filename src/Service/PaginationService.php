<?php


namespace App\Service;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaginationService extends AbstractController
{
    private $page;
    private $totalPages;
    private const resultsPerPage = 10;
    private $paginationLinks;

    public function __construct($page = 1, $totalResults = 1, $route)
    {
        $this->page = $page;
        $this->totalPages = ceil($totalResults / self::resultsPerPage);
        $this->createPagination($route);
    }

    public function getTotalPages() : int
    {
        return $this->totalPages;
    }

    public function getPage() : int
    {
        return $this->page;
    }

    public function getPaginationLinks() : array
    {
        return $this->paginationLinks;
    }

    private function createPagination($route) : void
    {
        if($this->totalPages < 5)
        {
            foreach (range(1, $this->totalPages) as $i)
            {
                $this->paginationLinks[] = $this->generateUrl($route, ['page' => $i]);
            }
        }
    }
}