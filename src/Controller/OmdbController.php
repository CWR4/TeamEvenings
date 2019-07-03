<?php

namespace App\Controller;

use App\Entity\Movie;
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
 *
 * @todo fix issue where you delete movie from a voting by replacing it with already existing one. adapt flash message.
 * @todo needs refactoring -> put more stuff to service -> empty this shit out
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
        $movies = null;
        $pagination = null;

        // Check if form was submitted and valid OR title is set in url
        if (isset($title) || ($form->isSubmitted() && $form->isValid())) {
            // If form was submitted get new movie title from form and set current page to 1 (for API call)
            if ($form->isSubmitted()) {
                $parameters['title'] = urlencode($form->get('Title')->getData());
                $parameters['page'] = 1;
            }

            // API call
            $result = $omdbService->searchByTitle($parameters);

            // Check if movies found and if pagination is needed
            if ($result['Response'] === 'True' && $result['totalResults'] > 10) {
                $paginationService->createPagination('omdb', $parameters, $result['totalResults']);
                $pagination = $paginationService->getPaginationLinks();
            }

            // Check for errors and create flash message
            // else: Not enough movies found for pagination
            if ($result['Response'] === 'False') {
                if ($result['Error'] === 'Too many results.') {
                    $this->addFlash('warning', 'Zu viele Ergebnisse. Bitte spezifizieren.');
                } elseif ($result['Error'] === 'Movie not found!') {
                    $this->addFlash('warning', 'Kein Film gefunden.');
                } else {
                    $this->addFlash('warning', $result['Error']);
                }
            } else {
                $movies = $omdbService->getResultsAsEntities($result['Search']);
            }
        }

        // Create form to add movie to event
        $addForm = $this->createForm(AddMovieType::class);
        $addForm->add('mid', HiddenType::class, ['data' => $mid]);
        $addForm->handleRequest($request);

        // Check if form was send
        if ($addForm->isSubmitted()) {
            // Get movie information from omdb
            $movieid = $addForm->getData()['movieid'];
            $movie = $omdbService->getDataById($movieid);

            // Check if movie already exist in db
            if ($this->getDoctrine()->getRepository(Movie::class)->findByImdbId($movie->getImdbID())) {
                $movie = $this->getDoctrine()->getRepository(Movie::class)->findByImdbId($movie->getImdbID());
                $movie->addVoting($movienight->getVoting());
            } else {
                $movienight->getVoting()->addMovie($movie);
            }

            if (!($addForm->getData()['mid'] === '0' || $addForm->getData()['mid'] === null)) {
                $oldmovie = $manager->getRepository(Movie::class)->find($mid);
                $movienight->getVoting()->removeMovie($oldmovie);
                $votingService->deleteVotes($movienight->getVoting(), $oldmovie);
            }

            $manager->persist($movie);
            $manager->persist($movienight->getVoting());
            $manager->flush();

            $this->addFlash('success', 'Film erfolgreich hinzugefügt');

            return $this->redirectToRoute('addMovie', ['vid' => $movienight->getVoting()->getId()]);
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
