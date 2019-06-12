<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\MovieNight;
use App\Entity\Voting;
use App\Form\AddMovieType;
use App\Form\MovieFormType;
use App\Service\OmdbService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\PaginationService;

/**
 * Class OmdbController
 * @package App\Controller
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
     * @param OmdbService $omdbService
     * @param Request $request
     * @param PaginationService $paginationService
     * @param $page
     * @param $title
     * @param $mnid
     * @return Response
     * @Route("/omdb/{mnid<\d+>?}/{vid<\d+>?}/{mid<\d+>?0}/{title<.*?>?}/{page<\d+>?1}", name="omdb")
     */
    public function addOmdbMovie(OmdbService $omdbService, Request $request, PaginationService $paginationService, Voting $vid, $mid, $page, $title, $mnid) : Response
    {
        // Get movienight from db
        $manager = $this->getDoctrine()->getManager();
        $movienight = $manager->getRepository(MovieNight::class)->find($mnid);

        // Create form for movie search
        $form = $this->createForm(MovieFormType::class);
        $form->handleRequest($request);

        // Set parameters for pagination and api call
        $parameters = ['mnid' => $mnid, 'title' => $title, 'page' => $page, 'vid' => $vid->getId(), 'mid' => $mid];

        // Set variables to null, so they won't show if not needed
        $movies = null;
        $pagination = null;

        // Check if form was submitted and valid OR title is set in url
        if(($form->isSubmitted() && $form->isValid()) || isset($title))
        {
            // If form was submitted get new movie title from form and set current page to 1 (for API call)
            if($form->isSubmitted())
            {
                $parameters['title'] = urlencode($form->get('Title')->getData());
                $parameters['page'] = 1;
            }

            // API call
            $result = $omdbService->searchByTitle($parameters);

            // Check if movies found and if pagination is needed
            if($result['Response'] === 'True' && $result['totalResults'] > 10)
            {
                $paginationService->createPagination('omdb', $parameters, $result['totalResults']);
                $pagination = $paginationService->getPaginationLinks();
            }

            // Check for errors and create flash message
            if($result['Response'] === 'False')
            {
                if($result['Error'] === 'Too many results.')
                {
                    $this->addFlash('warning', 'Zu viele Ergebnisse. Bitte spezifizieren.');
                }
                elseif($result['Error'] === 'Movie not found!')
                {
                    $this->addFlash('warning', 'Kein Film gefunden.');
                }
                else
                {
                    $this->addFlash('warning', $result['Error']);
                }
            }
            // Not enough movies found for pagination
            else
            {
                $movies = $omdbService->getResultsAsEntities($result['Search']);
            }
        }

        // Create form to add movie to event
        $addForm = $this->createForm(AddMovieType::class);
        $addForm->handleRequest($request);

        // Check if form was send
        if($addForm->isSubmitted())
        {
            // Get movie information from omdb
            $movieid = $addForm->getData()['movieid'];
            $movie = $omdbService->getDataById($movieid);

            // Check if movie already exist in db
            if($this->getDoctrine()->getRepository(Movie::class)->findByImdbId($movie->getImdbID()))
            {
                $movie = $this->getDoctrine()->getRepository(Movie::class)->findByImdbId($movie->getImdbID());
                $movie->addVoting($vid);
            }
            else
            {
                $vid->addMovie($movie);
            }

            $manager->persist($movie);
            $manager->persist($vid);
            $manager->flush();

            $this->addFlash('success', 'Film erfolgreich hinzugefügt');

            return $this->redirectToRoute('addMovie', ['vid' => $vid->getId()]);
        }

        return $this->render('omdb/index.html.twig', [
            'form' => $form->createView(),
            'movies' => $movies,
            'pagination' => $pagination,
            'title' => urldecode($title),
            'addform' => $addForm->createView(),
            'date' => $movienight,
        ]);
    }

}
