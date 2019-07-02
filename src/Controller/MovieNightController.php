<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\MovieNight;
use App\Entity\Voting;
use App\Form\EditMovieNightType;
use App\Form\MovieNightType;
use App\Service\MovieNightService;
use App\Service\VotingService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

/**
 * Class MovieNightController
 */
class MovieNightController extends AbstractController
{
    /*
     *  - form for creating a new event
     *  - date, time and location
     */
    /**
     * @param Request           $request           http request
     * @param MovieNightService $movieNightService dependency injection
     *
     * @return Response
     *
     * @Route("/movienight/create", name="movie_night")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function createMovieNight(Request $request, MovieNightService $movieNightService): Response
    {
        $movieNight = new MovieNight();

        $dateform = $this->createForm(MovieNightType::class, $movieNight);
        $dateform->handleRequest($request);

        if ($movieNightService->createMovieNight($dateform, $movieNight)) {
            return $this->redirectToRoute('list_movienight');
        }

        return $this->render('movie_night/index.html.twig', [
            'form' => $dateform->createView(),
        ]);
    }

    /*
     *  - overview of all movienights planned, only future ones.
     */
    /**
     * @Route("/movienight/all", name="list_movienight")
     *
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function listAll(): Response
    {
        $manager = $this->getDoctrine()->getManager()->getRepository(MovieNight::class);

        $movienights = $manager->findAllByDateAsc();

        return $this->render('movie_night/list.html.twig', [
            'movienights' => $movienights,
        ]);
    }

    /*
     *  - form for editing movienight date, time location
     *  - same as creating, except buttons
     *  - loaded with data from existing object
     *  - button save and abort
     *  - checks if date and time are in the future
     */
    /**
     * @param Request           $request           http request
     * @param MovieNightService $movieNightService dependency injection
     * @param int               $id                movienight id
     *
     * @return Response
     *
     * @Route("/movienight/edit/{id<\d+>}", name="edit_movienight")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function editMovieNight(Request $request, MovieNightService $movieNightService, $id): Response
    {
        $movieNight = $this->getDoctrine()->getRepository(MovieNight::class)->find($id);

        if (null === $movieNight) {
            $this->addFlash('warning', 'Termin wurde nicht gefunden');

            return $this->redirectToRoute('list_movienight');
        }

        $editForm = $this->createForm(EditMovieNightType::class, $movieNight);
        $editForm->handleRequest($request);

        if ($movieNightService->editMovieNight($editForm, $movieNight)) {
            return $this->redirectToRoute('list_movienight');
        }

        return $this->render('movie_night/edit.html.twig', [
            'form' => $editForm->createView(),
        ]);
    }

    /*
     *  - form for deleting movienight date
     *  - same as creating, except buttons
     *  - loaded with data from existing object
     */
    /**
     * @param Request           $request           http request
     * @param MovieNightService $movieNightService dependency injection
     * @param int               $id                movienight id
     *
     * @return Response
     *
     * @Route("/movienight/delete/{id<\d+>?}", name="delete_movienight")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteMovieNight(Request $request, MovieNightService $movieNightService, $id): Response
    {
        $movieNight = $this->getDoctrine()->getRepository(MovieNight::class)->find($id);

        if (null === $movieNight) {
            $this->addFlash('warning', 'Termin nicht gefunden');

            return $this->redirectToRoute('list_movienight');
        }

        $form = $this->createForm(MovieNightType::class, $movieNight);
        $form->handleRequest($request);

        if ($movieNightService->deleteMovieNight($form, $movieNight)) {
            return $this->redirectToRoute('list_movienight');
        }

        return $this->render('movie_night/delete.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param VotingService $votingService dependency injection
     * @param int           $mnid          movienight id
     * @param int           $mid           movie id
     *
     * @return Response
     *
     * @Route("/movienight/voting/{mnid<\d+>?}/{mid<\d+>?}", name="voting")
     *
     * @IsGranted("ROLE_USER")
     *
     * @throws Exception
     */
    public function voting(VotingService $votingService, $mnid, $mid): Response
    {
        $movienight = $this->getDoctrine()->getRepository(MovieNight::class)->find($mnid);

        $result = $votingService->getResult($mnid);

        if (isset($mid)) {
            $votingService->vote($mnid, $mid);

            return $this->redirectToRoute('voting', ['mnid' => $movienight->getId()]);
        }

        return $this->render('movie_night/voting.html.twig', [
            'result' => $result,
            'movienight' => $movienight,
        ]);
    }

    /*
     *  - page to connect movies to voting / movienight
     */
    /**
     * @param VotingService $votingService dependency injection
     * @param int           $vid           voting id
     *
     * @return Response
     *
     * @Route("/movienight/addMovie/{vid<\d+>?}", name="addMovie")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function addMovieToVoting(VotingService $votingService, $vid): Response
    {
        $movienight = $votingService->getMovieAndMovienight($vid);

        if (null === $movienight['movienight']) {
            $this->addFlash('warning', 'Filmabend wurde nicht gefunden');

            return $this->redirectToRoute('list_movienight');
        }

        return $this->render('movie_night/addMovie.html.twig', [
            'movies' => $movienight['movies'],
            'movienight' => $movienight['movienight'],
        ]);
    }

    /**
     * @param VotingService $votingService dependency injection
     * @param Voting        $vid           voting
     * @param Movie         $mid           movie
     *
     * @return Response
     *
     * @Route("/movienight/deleteMovie/{vid<\d+>?}/{mid<\d+>?}", name="deleteMovieFromVoting")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteMovieFromVoting(VotingService $votingService, Voting $vid, Movie $mid): Response
    {
        $votingService->deleteMovieFromVoting($vid, $mid);

        return $this->redirectToRoute('addMovie', ['vid' => $vid->getId()]);
    }
}
