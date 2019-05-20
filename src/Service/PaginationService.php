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

        if($this->totalPages < 5)
        {
            foreach (range(1, $this->totalPages) as $i)
            {
                $this->paginationLinks[] = $this->router->generate($route, ['page' => $i, 'title' => $query]);
            }
        }
        else
        {
            $this->paginationLinks = array(1,2,3,4,5);
        }
    }
}