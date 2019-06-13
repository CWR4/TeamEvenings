<?php


namespace App\Service;


use App\Entity\MovieNight;
use App\Entity\Vote;
use App\Entity\Voting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Movie;

class VotingService extends AbstractController
{
    /*
     *  -
     */
    public function getVotingResult($votingId) : ?array
    {
        // Get voting by id
        $result['voting'] = $this->getDoctrine()->getManager()->getRepository(Voting::class)->getVoting($votingId);

        if($result['voting'])
        {
            $result['votes'] = $this->getVotes($result['voting']);
            return $result;
        }

        return null;
    }

    /*
     *  - vote
     */
    public function vote(Voting $voting, $mid) : void
    {
        if(!$this->hasVoted($voting->getVotes()))
        {
            foreach($voting->getMovies() as $movie)
            {
                dump($movie);
                if($movie->getId() ===(int)$mid)
                {
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

    /*
     *  - checks if user already voted and add flash message
     */
    private function hasVoted($votes) : bool
    {
        $user = $this->getUser();

        if($votes)
        {
            foreach($votes as $vote)
            {
                if($user === $vote->getUser())
                {
                    $this->addFlash('warning', 'Sie haben bereits abgestimmt');
                    return true;
                }
            }
        }

        return false;
    }

    /*
     *  - creates array
     *  - array key => movie id
     *  - value => number of votes for movie
     */
    private function getVotes(Voting $voting) : array
    {
        $votes = [];

        foreach ($voting->getMovies() as $movie)
        {
            $votes[$movie->getId()] = 0;
        }

        foreach ($voting->getVotes() as $vote)
        {
            $votes[$vote->getMovie()->getId()]++;
        }

        return $votes;
    }

    /*
     *  - delete all votes in given voting for given movie
     *  - in case a movie gets removed from voting
     */
    public function deleteVotes(Voting $voting, Movie $movie) : void
    {
        $votes = $voting->getVotes();

        foreach ($votes as $vote)
        {
            if($vote->getMovie() === $movie)
            {
                $voting->removeVote($vote);
            }
        }

        $this->getDoctrine()->getManager()->persist($voting);
        $this->getDoctrine()->getManager()->flush();
    }

    public function getVotedMovieId(MovieNight $movieNight) : ?int
    {
        $votes = [];

        foreach ($movieNight->getVoting()->getMovies() as $movie)
        {
            $votes[$movie->getId()] = 0;
        }

        foreach ($movieNight->getVoting()->getVotes() as $vote)
        {
            $votes[$vote->getMovie()->getId()]++;
        }

        if($votes)
        {
            return array_keys($votes, max($votes))[0];
        }

        return null;
    }
}