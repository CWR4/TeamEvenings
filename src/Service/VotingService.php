<?php

namespace App\Service;

use App\Entity\MovieNight;
use App\Entity\Vote;
use App\Entity\Voting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Movie;

class VotingService extends AbstractController
{
    /**
     * @param $votingId
     * @return array|null
     */
    public function getVotingResult($votingId): ?array
    {
        // Get voting by id
        $result['voting'] = $this->getDoctrine()->getManager()->getRepository(Voting::class)->getVoting($votingId);

        if ($result['voting']) {
            $result['votes'] = $this->getVotes($result['voting']);
            return $result;
        }

        return null;
    }

    /**
     * @param $mnid
     * @param $mid
     */
    public function vote($mnid, $mid): void
    {
        $voting = $this->getDoctrine()->getRepository(MovieNight::class)->find($mnid)->getVoting();
        if (!$this->hasVoted($voting->getVotes())) {
            foreach ($voting->getMovies() as $movie) {
                if ($movie->getId() === (int)$mid) {
                    $vote = new Vote();
                    $vote->setUser($this->getUser());
                    $vote->setMovie($movie);
                    $vote->setVoting($voting);

                    $manager = $this->getDoctrine()->getManager();
                    $manager->persist($vote);
                    $manager->flush();

                    $this->addFlash('success', 'Erfolgreich abgestimmt!');
                }
            }
        }
    }

    /**
     * @param $votes
     * @return bool
     */
    private function hasVoted($votes): bool
    {
        $user = $this->getUser();

        if ($votes) {
            foreach ($votes as $vote) {
                if ($user === $vote->getUser()) {
                    $this->addFlash('warning', 'Sie haben bereits abgestimmt');
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param Voting $voting
     * @return array
     */
    private function getVotes(Voting $voting): array
    {
        $votes = [];

        foreach ($voting->getMovies() as $movie) {
            $votes[$movie->getId()] = 0;
        }

        foreach ($voting->getVotes() as $vote) {
            $votes[$vote->getMovie()->getId()]++;
        }

        return $votes;
    }

    /*
     *  - delete all votes in given voting for given movie
     *  - in case a movie gets removed from voting
     */
    /**
     * @param Voting $voting
     * @param Movie $movie
     */
    public function deleteVotes(Voting $voting, Movie $movie): void
    {
        $votes = $voting->getVotes();

        foreach ($votes as $vote) {
            if ($vote->getMovie() === $movie) {
                $voting->removeVote($vote);
            }
        }

        $this->getDoctrine()->getManager()->persist($voting);
        $this->getDoctrine()->getManager()->flush();
    }

    /**
     * @param MovieNight $movieNight
     * @return int|null
     */
    public function getVotedMovieId(MovieNight $movieNight): ?int
    {
        $votes = [];

        foreach ($movieNight->getVoting()->getMovies() as $movie) {
            $votes[$movie->getId()] = 0;
        }

        foreach ($movieNight->getVoting()->getVotes() as $vote) {
            $votes[$vote->getMovie()->getId()]++;
        }

        if ($votes) {
            return array_keys($votes, max($votes))[0];
        }

        return null;
    }

    /**
     * @param MovieNight $movieNight
     */
    public function updateMovieNightMovie(?MovieNight $movieNight): void
    {
        if ($movieNight !== null) {
            $nextMovieId = $this->getVotedMovieId($movieNight);
            $movieNight->setMovie($this->getDoctrine()->getRepository(Movie::class)->find($nextMovieId));
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($movieNight);
            $manager->flush();
        }
    }

    /**
     * @param $mnid
     * @return array|null
     */
    public function getResult($mnid): ?array
    {
        $movieNight = $this->getDoctrine()->getRepository(MovieNight::class)->find($mnid);

        if ($movieNight) {
            $voting = $movieNight->getVoting();
            return $this->getVotingResult($voting->getId());
        }
        $this->addFlash('warning', 'Filmabend nicht gefunden!');
        return null;
    }

    /**
     * @param $vid
     * @return array
     */
    public function getMovieAndMovienight($vid): array
    {
        $movienight = [
            'movienight' => null,
            'movies' => null
        ];

        if ($vid !== null) {
            $movienight['movienight'] = $this->getDoctrine()->getRepository(Voting::class)->find($vid)->getMovieNight();
            $movienight['movies'] = $movienight['movienight']->getVoting()->getMovies();
        }

        return $movienight;
    }

    /**
     * @param Voting $voting
     * @param Movie $movie
     */
    public function deleteMovieFromVoting(Voting $voting, Movie $movie): void
    {
        $manager = $this->getDoctrine()->getManager();
        $voting->removeMovie($movie);
        $this->deleteVotes($voting, $movie);
        $manager->persist($voting);
        $manager->flush();

        if ($voting->getMovies() === null) {
            $voting->getMovieNight()->setMovie(null);
        }

        $this->addFlash('success', 'Film erfolgreich entfernt!');
    }
}