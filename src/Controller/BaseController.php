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
     * @Route("/", name="base")
     */
    public function index(VotingService $votingService) : Response
    {
        $movienight = $this->getDoctrine()->getRepository(MovieNight::class)->getNextMovienight();

        dump($movienight);

        if($movienight)
        {
            $nextMovieId = $votingService->getVotedMovieId($movienight);
            $movienight->setMovie($this->getDoctrine()->getRepository(Movie::class)->find($nextMovieId));
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($movienight);
            $manager->flush();

            $movienight =  $this->getDoctrine()->getRepository(MovieNight::class)->getNextMovienight();
        }

        return $this->render('base/index.html.twig', [
            'movienight' => $movienight,
        ]);
    }
}
