<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\MovieNight;
use App\Entity\Voting;
use App\Form\MovieNightType;
use App\Form\EditMovieNightType;
use App\Repository\VotingRepository;
use App\Service\VotingService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MovieNightController
 * @package App\Controller
 */

class MovieNightController extends AbstractController
{
    /*
     *  - form for creating a new event
     *  - date, time and location
     */
    /**
     * @param Request $request
     * @return Response
     * @Route("/movienight/create", name="movie_night")
     * @IsGranted("ROLE_ADMIN")
     */
    public function createMovieNight(Request $request) : Response
    {
        $manager = $this->getDoctrine()->getManager();

        $movienight = new MovieNight();
        $dateform = $this->createForm(MovieNightType::class, $movienight);
        $dateform->handleRequest($request);

        if($dateform->isSubmitted() && $dateform->isValid())
        {
            if($movienight->getDate()->format('Y.m.d') < date('Y.m.d'))
            {
                $this->addFlash('warning', 'Datum ist vergangen!');
            }
            elseif($movienight->getDate()->format('d.m.Y') ===  date('d.m.Y') && $movienight->getTime()->format('H:i') < date('H:i', time() - 900))
            {
                $this->addFlash('warning', 'Zeitpunkt ist vergangen!');
            }
            else
            {
                $voting = new Voting();
                $voting->setOpen(true);

                $movienight->setVoting($voting);

                $manager->persist($voting);
                $manager->persist($movienight);

                $manager->flush();
                $this->addFlash('success', 'Termin erstellt!');

                return $this->redirectToRoute('list_movienight');
            }
        }

        return $this->render('movie_night/index.html.twig', [
            'form' => $dateform->createView()
        ]);
    }

    /*
     *  - overview of all movienights planned, only future ones.
     */
    /**
     * @Route("/movienight/all", name="list_movienight")
     * @IsGranted("ROLE_USER")
     */
    public function listAll() : Response
    {
        $manager = $this->getDoctrine()->getRepository(MovieNight::class);

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
     * @param Request $request
     * @param $id
     * @return Response
     * @Route("/movienight/edit/{id<\d+>}", name="edit_movienight")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editMovieNight(Request $request, $id) : Response
    {
        $manager = $this->getDoctrine()->getManager();
        $movieNight = $manager->getRepository(MovieNight::class)->find($id);

        // check if event was found in database -> flash message if not and redirection
        if($movieNight === null)
        {
            $this->addFlash('warning', 'Termin wurde nicht gefunden');
            return $this->redirectToRoute('list_movienight');
        }

        // create form and hand data from object to it
        $editForm = $this->createForm(EditMovieNightType::class, $movieNight);
        $editForm->handleRequest($request);

        // check if form is submitted and valid
        if($editForm->isSubmitted() && $editForm->isValid())
        {
            // check date has passed
            if($movieNight->getDate()->format('Y.m.d') < date('Y.m.d'))
            {
                $this->addFlash('warning', 'Datum ist vergangen!');
            }
            // check if time has passed if date is today
            elseif($movieNight->getDate()->format('d.m.Y') ===  date('d.m.Y') && $movieNight->getTime()->format('H:i') < date('H:i', time() - 900))
            {
                $this->addFlash('warning', 'Zeitpunkt ist vergangen!');
            }
            // else save object to database, redirect and show flash message
            else
            {
                $manager->persist($movieNight);
                $manager->flush();

                $this->addFlash('success', 'Termin erfolgreich geändert');

                return $this->redirectToRoute('list_movienight');
            }
        }

        return $this->render('movie_night/edit.html.twig', [
            'form' => $editForm->createView()
        ]);
    }

    /*
     *  - form for deleting movienight date
     *  - same as creating, except buttons
     *  - loaded with data from existing object
     */
    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @Route("/movienight/delete/{id<\d+>?}", name="delete_movienight")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteMovieNight(Request $request, $id) : Response
    {
        $manager = $this->getDoctrine()->getManager();

        $date = $manager->getRepository(MovieNight::class)->find($id);

        // check if event exists and it's in the future, empty form if event passed
        if($date === null ||
            $date->getDate()->format('Y.m.d') <= date('Y.m.d') ||
            ($date->getDate()->format('Y.m.d') <= date('Y.m.d') &&
            $date->getTime()->format('H:i') <= date('H:i')))
        {
            $form = $this->createForm(MovieNightType::class);
            $this->addFlash('warning', 'Termin nicht gefunden');
        }
        else
        {
            $form = $this->createForm(MovieNightType::class, $date);
        }

        $form->handleRequest($request);

        // check if submitted and delete if so
        if($form->isSubmitted())
        {
            $manager->remove($date);
            $manager->flush();

            return $this->redirectToRoute('list_movienight');
        }

        return $this->render('movie_night/delete.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /*
     *  - voting page
     */
    /**
     * @param VotingService $votingService
     * @param $mid
     * @param $mnid
     * @return Response
     * @Route("/movienight/voting/{mnid<\d+>?}/{mid<\d+>?}", name="voting")
     * @IsGranted("ROLE_USER")
     */
    public function voting(VotingService $votingService, $mnid, $mid) : Response
    {
        $movienight = $this->getDoctrine()->getRepository(MovieNight::class)->find($mnid);

        if($movienight)
        {
            $voting = $movienight->getVoting();
            $result = $votingService->getVotingResult($voting->getId());
        }
        else
        {
            $result = null;
        }

        if(isset($mid)) {
            $votingService->vote($voting, $mid);
            return $this->redirectToRoute('voting', ['mnid' => $movienight->getId()]);
        }

        return $this->render('movie_night/voting.html.twig', [
            'result' => $result,
            'movienight' => $movienight
        ]);
    }

    /*
     *  - page to connect movies to voting / movienight
     */
    /**
     * @param $vid
     * @return Response
     * @Route("/movienight/addMovie/{vid<\d+>?}", name="addMovie")
     * @IsGranted("ROLE_ADMIN")
     */
    public function addMovieToVote($vid) : Response
    {
        if(isset($vid))
        {
            $voting = $this->getDoctrine()->getManager()->getRepository(Voting::class)->getVoting($vid);
            $movies = $voting->getMovies();
            $movienight = $voting->getMovieNight();
        }

        return $this->render('movie_night/addMovie.html.twig', [
            'movies' => $movies,
            'movienight' => $movienight,
        ]);
    }

    /**
     * @param $vid
     * @param $mid
     * @param VotingService $votingService
     * @return Response
     * @Route("/movienight/deleteMovie/{vid<\d+>?}/{mid<\d+>?}", name="deleteMovieFromVoting")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteMovieFromVoting(VotingService $votingService, Voting $vid, Movie $mid) : Response
    {
        $manager = $this->getDoctrine()->getManager();
        $vid->removeMovie($mid);
        $votingService->deleteVotes($vid, $mid);
        $manager->persist($vid);
        $manager->flush();

        return $this->redirectToRoute('addMovie', ['vid' => $vid->getId()]);
    }
}
