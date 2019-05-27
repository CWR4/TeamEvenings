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

    public function createPagination($route, $parameters, $totalResults) : void
    {
        $this->page = $parameters['page'];
        $this->totalPages = (int)ceil($totalResults / self::resultsPerPage);


        if($this->page <= 3 || $this->totalPages <= 5)
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
                $parameters['page'] = $i;
                $this->paginationLinks[$i] = $this->router->generate($route, $parameters);
            }
            if($this->totalPages > 5)
            {
                $parameters['page'] = $this->totalPages;
                $this->paginationLinks['&raquo;'] = $this->router->generate($route, $parameters);
            }
        }
        elseif($this->page >= $this->totalPages-2)
        {
            $parameters['page'] = 1;
            $this->paginationLinks['&laquo;'] = $this->router->generate($route, $parameters);
            for($i = $this->totalPages-4; $i <= $this->totalPages; $i++)
            {
                $parameters['page'] = $i;
                $this->paginationLinks[$i] = $this->router->generate($route, $parameters);
            }
        }
        else
        {
            $parameters['page'] = 1;
            $this->paginationLinks['&laquo;'] = $this->router->generate($route, $parameters);
            for($i = $this->page-2; $i <= $this->page+2; $i++)
            {
                $parameters['page'] = $i;
                $this->paginationLinks[$i] = $this->router->generate($route, $parameters);
            }
            $parameters['page'] = $this->totalPages;
            $this->paginationLinks['&raquo;'] = $this->router->generate($route, $parameters);
        }
    }
}