<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\MovieNight;
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
     * @return Response
     * @Route("/", name="base")
     */
    public function index(VotingService $votingService) : Response
    {
        $i = 0;
        do{
            $movieNight = $this->getDoctrine()->getRepository(MovieNight::class)->getNextMovienight($i);
            $i++;
        } while ( isset($movieNight) && $movieNight->getVoting()->getMovies()->isEmpty());

        $votingService->updateMovieNightMovie($movieNight);

        return $this->render('base/index.html.twig', [
            'movienight' => $movieNight,
        ]);
    }
}
