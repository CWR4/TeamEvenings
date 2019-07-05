<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\MovieNight;
use App\Entity\Voting;
use App\Form\EditMovieNightType;
use App\Form\MovieNightType;
use App\Service\MovieNightService;
use App\Service\VotingService;

use Doctrine\Common\Persistence\ObjectManager;

use Exception;

use Psr\Log\LoggerInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MovieNightController
 *
 * @Route("/movienight/", name="movie_night_")
 */
class MovieNightController extends AbstractController
{

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * MovieNightController constructor.
     *
     * @param ObjectManager   $manager for db requests
     * @param LoggerInterface $logger  for writing logs
     */
    public function __construct(ObjectManager $manager, LoggerInterface $logger)
    {
        $this->manager = $manager;
        $this->logger = $logger;
    }

    /**
     *  - form for creating a new event
     *  - date, time and location
     *
     * @param Request           $request           http request
     * @param MovieNightService $movieNightService dependency injection
     *
     * @return Response
     *
     * @Route("create", name="create")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function createMovieNight(Request $request, MovieNightService $movieNightService): Response
    {
        $movieNight = new MovieNight();

        $dateform = $this->createForm(MovieNightType::class, $movieNight);
        $dateform->handleRequest($request);

        if ($movieNightService->createMovieNight($dateform, $movieNight)) {
            return $this->redirectToRoute('movie_night_list_all');
        }

        return $this->render('movie_night/index.html.twig', [
            'form' => $dateform->createView(),
        ]);
    }

    /*
     *  - overview of all movienights planned, only future ones.
     */
    /**
     * @Route("all", name="list_all")
     *
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function listAll(): Response
    {
        $movienights = $this->getDoctrine()->getRepository(MovieNight::class)->findAllByDateAsc();

        $this->logger->debug('Debug');

        return $this->render('movie_night/list.html.twig', [
            'movienights' => $movienights,
        ]);
    }

    /**
     *  - form for editing movienight date, time location
     *  - same as creating, except buttons
     *  - loaded with data from existing object
     *  - button save and abort
     *  - checks if date and time are in the future
     *
     * @param Request           $request           http request
     * @param MovieNightService $movieNightService dependency injection
     * @param MovieNight        $movieNight        movienight id
     *
     * @return Response
     *
     * @Route("edit/{movieNight}", name="edit")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function editMovieNight(Request $request, MovieNightService $movieNightService, MovieNight $movieNight): Response
    {
        $editForm = $this->createForm(EditMovieNightType::class, $movieNight);
        $editForm->handleRequest($request);

        if ($movieNightService->editMovieNight($editForm, $movieNight)) {
            return $this->redirectToRoute('movie_night_list_all');
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
     * @param MovieNight        $movieNight        movienight
     *
     * @return Response
     *
     * @Route("delete/{id<\d+>?}", name="delete")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteMovieNight(Request $request, MovieNightService $movieNightService, MovieNight $movieNight): Response
    {
        $form = $this->createForm(MovieNightType::class, $movieNight);
        $form->handleRequest($request);

        if ($movieNightService->deleteMovieNight($form, $movieNight)) {
            return $this->redirectToRoute('movie_night_list_all');
        }

        return $this->render('movie_night/delete.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param VotingService $votingService dependency injection
     * @param MovieNight    $movieNight    movienight
     * @param Movie|null    $movie         movie
     *
     * @return Response
     *
     * @Route("voting/{movieNight<\d+>?}/{movie<\d+>?}", name="voting")
     *
     * @IsGranted("ROLE_USER")
     *
     * @throws Exception
     */
    public function voting(VotingService $votingService, MovieNight $movieNight, ?Movie $movie): Response
    {
        $result = $votingService->getResult($movieNight);

        if (isset($movie)) {
            $votingService->vote($movieNight, $movie);

            return $this->redirectToRoute('movie_night_voting', ['movieNight' => $movieNight->getId()]);
        }

        return $this->render('movie_night/voting.html.twig', [
            'result' => $result,
            'movienight' => $movieNight,
        ]);
    }

    /**
     *  - page to connect movies to voting / movienight
     *
     * @param MovieNight $movieNight movienight
     *
     * @return Response
     *
     * @Route("addMovie/{movieNight<\d+>?}", name="addMovie")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function addMovieToMovieNight(MovieNight $movieNight): Response
    {
        if (null === $movieNight) {
            $this->addFlash('warning', 'Filmabend wurde nicht gefunden');

            return $this->redirectToRoute('movie_night_list_all');
        }

        return $this->render('movie_night/addMovie.html.twig', [
            'movies' => $movieNight->getMovies(),
            'movienight' => $movieNight,
        ]);
    }

    /**
     * @param VotingService $votingService dependency injection
     * @param MovieNight    $movieNight    movie night
     * @param Movie         $movie         movie
     *
     * @return Response
     *
     * @Route("deleteMovie/{movieNight<\d+>?}/{movie<\d+>?}", name="deleteMovieFromVoting")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteMovieFromMovieNight(VotingService $votingService, MovieNight $movieNight, Movie $movie): Response
    {
        $votingService->deleteMovieFromMovieNight($movieNight, $movie);

        return $this->redirectToRoute('movie_night_addMovie', ['movieNight' => $movieNight->getId()]);
    }
}
