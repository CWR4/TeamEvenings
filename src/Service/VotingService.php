<?php

namespace App\Service;

use App\Entity\MovieNight;
use App\Entity\Vote;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Movie;
use Exception;

/**
 * Class VotingService
 */
class VotingService extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * VotingService constructor.
     *
     * @param ObjectManager $manager to handle orm entities
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param MovieNight $movieNight movienight
     *
     * @throws Exception
     *
     * @return array|null
     */
    public function getVotingResult(MovieNight $movieNight): ?array
    {
        // Get voting by id
        $result['movieNight'] = $movieNight;

        if ($result['movieNight']) {
            $result['votes'] = $this->getVotes($result['movieNight']);

            return $result;
        }

        return null;
    }

    /**
     * @param MovieNight $movieNight movienight
     * @param Movie      $movie      movie
     */
    public function vote(MovieNight $movieNight, Movie $movie): void
    {
        if ($movieNight && !$this->hasVoted($movieNight->getVotes())) {
            $vote = new Vote();
            $vote->setUser($this->getUser());
            $vote->setMovie($movie);
            $vote->setMovieNight($movieNight);

            $this->manager->persist($vote);
            $this->manager->flush();

            $this->addFlash('success', 'Erfolgreich abgestimmt!');
        }
    }

    /**
     *  - delete all votes in given movienight for given movie
     *  - in case a movie gets removed from voting
     *
     * @param MovieNight $movieNight as parameter
     * @param Movie      $movie      delete votes for movie in voting
     */
    public function deleteVotes(MovieNight $movieNight, Movie $movie): void
    {
        $votes = $movieNight->getVotes();

        foreach ($votes as $vote) {
            if ($vote->getMovie() === $movie) {
                $movieNight->removeVote($vote);
            }
        }
        $this->manager->flush();
    }

    /**
     * @param MovieNight $movieNight movienight
     *
     * @throws Exception
     *
     * @return array|null
     */
    public function getResult(MovieNight $movieNight): ?array
    {
        if ($movieNight) {
            return $this->getVotingResult($movieNight);
        }
        $this->addFlash('warning', 'Filmabend nicht gefunden!');

        return null;
    }

    /**
     * @param int $vid voting id
     *
     * @return array
     */
    public function getMovieAndMovienight($vid): array
    {
        $movienight = [
            'movienight' => null,
            'movies' => null,
        ];

        if (null !== $vid) {
            $movienight['movienight'] = $this->getDoctrine()->getRepository(Voting::class)->find($vid)->getMovieNight();
            $movienight['movies'] = $movienight['movienight']->getVoting()->getMovies();
        }

        return $movienight;
    }

    /**
     * @param MovieNight $movieNight current voting as parameter
     * @param Movie      $movie      movie as parameter
     */
    public function deleteMovieFromMovieNight(MovieNight $movieNight, Movie $movie): void
    {
        $movieNight->removeMovie($movie);
        $this->deleteVotes($movieNight, $movie);
        $this->manager->flush();

        $this->addFlash('success', 'Film erfolgreich entfernt!');
    }

    /**
     * @param MovieNight $movieNight movienight
     *
     * @return Movie|null
     */
    public function getMostVotedMovie(MovieNight $movieNight): ?Movie
    {
    }

    /**
     * @param array|null $votes array with all votes of voting
     *
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
     * @param MovieNight $movieNight movienight
     *
     * @return array
     */
    private function getVotes(MovieNight $movieNight): array
    {
        $votes = [];

        foreach ($movieNight->getMovies() as $movie) {
            $votes[$movie->getId()] = 0;
        }

        foreach ($movieNight->getVotes() as $vote) {
            ++$votes[$vote->getMovie()->getId()];
        }

        return $votes;
    }
}
