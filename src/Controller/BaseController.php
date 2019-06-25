<?php

namespace App\Controller;

use App\Entity\MovieNight;
use App\Service\MovieNightService;
use App\Service\VotingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    /*
     *  - landing page
     *  - displays next event
     */
    /**
     * @param VotingService $votingService
     * @param MovieNightService $movieNightService
     * @return Response
     * @Route("/", name="base")
     */
    public function index(VotingService $votingService, MovieNightService $movieNightService) : Response
    {
        $movieNight = $movieNightService->getNextMovieNight();

        $votingService->updateMovieNightMovie($movieNight);

        return $this->render('base/index.html.twig', [
            'movienight' => $movieNight,
        ]);
    }
}
