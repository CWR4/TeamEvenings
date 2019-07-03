<?php

namespace App\Controller;

use App\Entity\MovieNight;
use App\Form\AddMovieType;
use App\Form\MovieFormType;
use App\Service\OmdbService;
use App\Service\PaginationService;
use App\Service\VotingService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

/**
 * Class OmdbController
 * @IsGranted("ROLE_USER")
 */

class OmdbController extends AbstractController
{
    /*
     *  - search movie in open movie database
     *  - relate movie to movienight
     *  - store movie in database
     */
    /**
     * @param OmdbService       $omdbService       dependency injection
     * @param VotingService     $votingService     dependency injection
     * @param Request           $request           http request
     * @param PaginationService $paginationService dependency injection
     * @param int               $mid               movie id
     * @param int               $page              current page
     * @param string            $title             title of movie as string
     * @param int               $mnid              movienight id
     *
     * @throws Exception
     *
     * @return Response
     *
     * @Route("/omdb/{mnid<\d+>?}/{mid<\d+>?0}/{title<.*?>?}/{page<\d+>?1}", name="omdb")
     */
    public function addOmdbMovie(OmdbService $omdbService, VotingService $votingService, Request $request, PaginationService $paginationService, $mid, $page, $title, $mnid) : Response
    {
        // Get movienight from db
        $manager = $this->getDoctrine()->getManager();
        $movienight = $manager->getRepository(MovieNight::class)->find($mnid);

        // Create form for movie search
        $form = $this->createForm(MovieFormType::class);
        $form->handleRequest($request);

        // Set parameters for pagination and api call
        $parameters = ['mnid' => $mnid, 'title' => $title, 'page' => $page, 'mid' => $mid];

        // Set variables to null, so they won't show if not needed
        $movies = [];
        $pagination = [];

        // Process form, get pagination and movies
        $omdbService->processAndUpdateOmdbRequest($paginationService, $form, $parameters, $pagination, $movies);

        // Create form to add movie to event
        $addForm = $this->createForm(AddMovieType::class);
        $addForm->add('mid', HiddenType::class, ['data' => $mid]);
        $addForm->handleRequest($request);

        // Check if form was send and process it
        if ($omdbService->processAddForm($votingService, $addForm, $movienight)) {
            return $this->redirectToRoute('addMovie', ['vid' => $movienight->getVoting()->getId()]);
        }

        return $this->render('omdb/index.html.twig', [
            'form' => $form->createView(),
            'movies' => $movies,
            'pagination' => $pagination,
            'title' => urldecode($parameters['title']),
            'addform' => $addForm->createView(),
            'date' => $movienight,
        ]);
    }
}
