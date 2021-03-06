<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\MovieNight;
use App\Form\MovieNightType;
use App\Service\OmdbService;
use App\Service\VotingService;

use Doctrine\Common\Persistence\ObjectManager;

use Exception;

use Psr\Log\LoggerInterface;

use Psr\Log\LogLevel;
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
     * @var OmdbService
     */
    private $omdbService;

    /**
     * MovieNightController constructor.
     *
     * @param ObjectManager   $manager     for db requests
     * @param LoggerInterface $logger      for writing logs
     * @param OmdbService     $omdbService for fetching data from omdb
     */
    public function __construct(ObjectManager $manager, LoggerInterface $logger, OmdbService $omdbService)
    {
        $this->manager = $manager;
        $this->logger = $logger;
        $this->omdbService = $omdbService;
    }

    /**
     *  - form for creating a new event
     *  - date, time and location
     *
     * @Route("create", name="create")
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request http request
     *
     * @return Response
     */
    public function create(Request $request): Response
    {
        $movieNight = new MovieNight();

        $createMovieNightForm = $this->createForm(MovieNightType::class, $movieNight);
        $createMovieNightForm->handleRequest($request);

        if ($createMovieNightForm->isSubmitted() && $createMovieNightForm->isValid()) {
            $this->manager->persist($movieNight);
            $this->manager->flush();

            $this->logger->log(LogLevel::INFO, 'Termin erstellt');
            $this->addFlash('success', 'Termin erfolgreich erstellt!');

            return $this->redirectToRoute('movie_night_list_all');
        }

        return $this->render('movie_night/index.html.twig', [
            'form' => $createMovieNightForm->createView(),
        ]);
    }

    /**
     * - overview of all movienights planned, only future ones.
     *
     * @Route("all", name="list_all")
     *
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function listAll(): Response
    {
        $movieNights = $this->getDoctrine()
            ->getRepository(MovieNight::class)
            ->findAllByDateAsc();

        return $this->render('movie_night/list.html.twig', [
            'movienights' => $movieNights,
        ]);
    }

    /**
     *  - form for editing movienight date, time location
     *  - loaded with data from existing object
     *  - button save and abort
     *
     * @Route("{movieNight}/edit", name="edit")
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request    $request    http request
     * @param MovieNight $movieNight movienight id
     *
     * @return Response
     */
    public function edit(Request $request, MovieNight $movieNight): Response
    {
        $editMovieNightForm = $this->createForm(MovieNightType::class, $movieNight);
        $editMovieNightForm->handleRequest($request);

        if ($editMovieNightForm->isSubmitted() && $editMovieNightForm->isValid()) {
            $this->manager->flush();

            $this->addFlash('success', 'Termin erfolgreich geändert!');

            return $this->redirectToRoute('movie_night_list_all');
        }

        return $this->render('movie_night/edit.html.twig', [
            'form' => $editMovieNightForm->createView(),
        ]);
    }

    /**
     *  - form for deleting movienight date
     *  - loaded with data from existing object
     *
     * @Route("{movieNight}/delete", name="delete")
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param MovieNight $movieNight movienight
     *
     * @return Response
     */
    public function delete(MovieNight $movieNight): Response
    {
        $this->manager->remove($movieNight);
        $this->manager->flush();

        $this->addFlash('success', 'Termin erfolgreich gelöscht!');

        return $this->redirectToRoute('movie_night_list_all');
    }

    /**
     * @Route("{movieNight}/voting/{movie<\d+>?}", name="voting")
     *
     * @IsGranted("ROLE_USER")
     *
     * @param VotingService $votingService dependency injection
     * @param MovieNight    $movieNight    movienight
     * @param Movie|null    $movie         movie
     *
     * @throws Exception
     *
     * @return Response
     */
    public function voting(VotingService $votingService, MovieNight $movieNight, ?Movie $movie): Response
    {
        $result = $votingService->getVotes($movieNight);

        if (isset($movie)) {
            $votingService->vote($movieNight, $movie);

            return $this->redirectToRoute('movie_night_voting', [
                'movieNight' => $movieNight->getId(),
            ]);
        }

        return $this->render('movie_night/voting.html.twig', [
            'result' => $result,
            'movieNight' => $movieNight,
        ]);
    }

    /**
     *  - page to connect movies to voting / movienight
     *
     * @Route("{movieNight}/addMovie/{imdbId?}", name="add_movie")
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param MovieNight  $movieNight movienight
     * @param string|null $imdbId     omdb movie id
     *
     * @throws Exception
     *
     * @return Response
     */
    public function addMovieToMovieNight(MovieNight $movieNight, ?string $imdbId): Response
    {
        if ($imdbId) {
            try {
                $this->omdbService->addMovie($movieNight, $imdbId);
            } catch (Exception $exception) {
                $this->logger->error($exception);
                $this->addFlash('error', 'Film konnte nicht hinzugefügt werden!');
            }

            return $this->redirectToRoute('movie_night_add_movie', [
                'movieNight' => $movieNight->getId(),
            ]);
        }

        return $this->render('movie_night/addMovie.html.twig', [
            'movies' => $movieNight->getMovies(),
            'movienight' => $movieNight,
        ]);
    }

    /**
     * @Route("{movieNight}/deleteMovie/{movie}", name="delete_movie")
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param VotingService $votingService dependency injection
     * @param MovieNight    $movieNight    movie night
     * @param Movie         $movie         movie
     *
     * @return Response
     */
    public function deleteMovieFromMovieNight(VotingService $votingService, MovieNight $movieNight, Movie $movie): Response
    {
        $movieNight->removeMovie($movie);
        $votingService->deleteVotes($movieNight, $movie);
        $this->manager->flush();

        $this->addFlash('success', 'Film erfolgreich entfernt!');

        return $this->redirectToRoute('movie_night_add_movie', [
            'movieNight' => $movieNight->getId(),
        ]);
    }
}
