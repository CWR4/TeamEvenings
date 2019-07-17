<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class PaginationService
 */
class PaginationService extends AbstractController
{
    private const RESULTSPERPAGE = 10;

    private $page;
    private $totalPages;
    private $paginationLinks;
    private $router;

    /**
     * PaginationService constructor.
     * @param UrlGeneratorInterface $router dependency injection for generating urls
     */
    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return int
     */
    public function getTotalPages() : int
    {
        return $this->totalPages;
    }

    /**
     * @return int
     */
    public function getPage() : int
    {
        return $this->page;
    }

    /**
     * @return array
     */
    public function getPaginationLinks() : array
    {
        return $this->paginationLinks;
    }

    /*
     *  Creates Pagination links
     *  - takes route to link to
     *  - parameters -> to be added to url to route
     *  - number of total results -> to calculate number of pages and current links
     */
    /**
     * @param string $route        route to generate links for
     * @param array  $parameters   array of parameters for link
     * @param int    $totalResults number of total results
     */
    public function createPagination($route, $parameters, $totalResults) : void
    {
        $this->page = $parameters['page'];
        $this->totalPages = (int) ceil($totalResults / self::RESULTSPERPAGE);


        if ($this->page <= 3 || $this->totalPages <= 5) {
            if ($this->totalPages >= 5) {
                $limit = 5;
            } else {
                $limit = $this->totalPages;
            }
            for ($i = 1; $i <= $limit; ++$i) {
                $parameters['page'] = $i;
                $this->paginationLinks[$i] = $this->router->generate($route, $parameters);
            }
            if ($this->totalPages > 5) {
                $parameters['page'] = $this->totalPages;
                $this->paginationLinks['&raquo;'] = $this->router->generate($route, $parameters);
            }
        } elseif ($this->page >= $this->totalPages-2) {
            $parameters['page'] = 1;
            $this->paginationLinks['&laquo;'] = $this->router->generate($route, $parameters);
            for ($i = $this->totalPages-4; $i <= $this->totalPages; ++$i) {
                $parameters['page'] = $i;
                $this->paginationLinks[$i] = $this->router->generate($route, $parameters);
            }
        } else {
            $parameters['page'] = 1;
            $this->paginationLinks['&laquo;'] = $this->router->generate($route, $parameters);
            for ($i = $this->page-2; $i <= $this->page+2; ++$i) {
                $parameters['page'] = $i;
                $this->paginationLinks[$i] = $this->router->generate($route, $parameters);
            }
            $parameters['page'] = $this->totalPages;
            $this->paginationLinks['&raquo;'] = $this->router->generate($route, $parameters);
        }
    }
}
