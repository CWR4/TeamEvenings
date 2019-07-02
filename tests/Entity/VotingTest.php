<?php

namespace App\Tests\App\Entity;

use App\Entity\Movie;
use App\Entity\MovieNight;
use App\Entity\Vote;
use App\Entity\Voting;
use PHPUnit\Framework\TestCase;

/**
 * Class VotingTest
 */
class VotingTest extends TestCase
{
    /**
     * Test add / get / remove movie.
     */
    public function testCanGetAddRemoveMovie(): void
    {
        $voting = new Voting();
        $this->assertEmpty($voting->getMovies());
        $testMovie = new Movie();
        $voting->addMovie($testMovie);
        $this->assertCount(1, $voting->getMovies());
        $voting->removeMovie($testMovie);
        $this->assertEmpty($voting->getMovies());
    }

    /**
     * Test add / get / remove vote.
     */
    public function testCanGetAddRemoveVote(): void
    {
        $voting = new Voting();
        $this->assertEmpty($voting->getVotes(), 'Not created empty!');
        $testVote = new Vote();
        $voting->addVote($testVote);
        $this->assertCount(1, $voting->getVotes(), 'Numbers don\'t match');
        $voting->removeVote($testVote);
        $this->assertEmpty($voting->getVotes(), 'Vote konnte nicht removed werden.');
    }

    /**
     * Test open / close voting.
     */
    public function testCanOpenAndCloseVoting(): void
    {
        $voting = new Voting();
        $this->assertEmpty($voting->getOpen());
        $voting->setOpen(true);
        $this->assertTrue($voting->getOpen());
        $voting->setOpen(false);
        $this->assertFalse($voting->getOpen());
    }

    /**
     * Test set / get movieNight.
     */
    public function testCanSetAndGetMovieNight(): void
    {
        $voting = new Voting();
        $this->assertEmpty($voting->getMovieNight());
        $testMovieNight = new MovieNight();
        $voting->setMovieNight($testMovieNight);
        $this->assertSame($testMovieNight, $voting->getMovieNight());
    }
}
