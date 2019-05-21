<?php


namespace App\Service;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaginationService extends AbstractController
{
    private $page;
    private $totalPages;
    private const resultsPerPage = 10;
    private $paginationLinks;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
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

    public function createPagination($route, $page, $totalResults, $query) : void
    {
        $this->page = $page;
        $this->totalPages = (int)ceil($totalResults / self::resultsPerPage);

        if($page <= 3 || $this->totalPages <= 5)
        {
            if($this->totalPages >= 5)
            {
                $limit = 5;
            }
            else
            {
                $limit = $this->totalPages;
            }
            for($i = 1; $i <= $limit; $i++)
            {
                $this->paginationLinks[$i] = $this->router->generate($route, ['page' => $i, 'title' => $query]);
            }
            if($this->totalPages > 5)
            {
                $this->paginationLinks['&raquo;'] = $this->router->generate($route, ['page' => $this->totalPages, 'title' => $query]);
            }
        }
        elseif($page >= $this->totalPages-2)
        {
            $this->paginationLinks['&laquo;'] = $this->router->generate($route, ['page' => 1, 'title' => $query]);
            for($i = $this->totalPages-4; $i <= $this->totalPages; $i++)
            {
                $this->paginationLinks[$i] = $this->router->generate($route, ['page' => $i, 'title' => $query]);
            }
        }
        else
        {
            $this->paginationLinks['&laquo;'] = $this->router->generate($route, ['page' => 1, 'title' => $query]);
            for($i = $page-2; $i <= $page+2; $i++)
            {
                $this->paginationLinks[$i] = $this->router->generate($route, ['page' => $i, 'title' => $query]);
            }
            $this->paginationLinks['&raquo;'] = $this->router->generate($route, ['page' => $this->totalPages, 'title' => $query]);
        }
    }
}