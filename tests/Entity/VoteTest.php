<?php

namespace App\Tests\App\Entity;

use App\Entity\User;
use App\Entity\Vote;
use App\Entity\Voting;
use App\Entity\Movie;
use PHPUnit\Framework\TestCase;

/**
 * Class VoteTest
 */
class VoteTest extends TestCase
{
    /**
     * Test set / get voting.
     */
    public function testCanSetAndGetVoting(): void
    {
        $vote = new Vote();
        $this->assertEmpty($vote->getVoting());
        $testVoting = new Voting();
        $vote->setVoting($testVoting);
        $this->assertSame($testVoting, $vote->getVoting());
    }

    /**
     * Test set / get movie.
     */
    public function testCanSetAndGetMovie(): void
    {
        $vote = new Vote();
        $this->assertEmpty($vote->getMovie());
        $testMovie = new Movie();
        $vote->setMovie($testMovie);
        $this->assertSame($testMovie, $vote->getMovie());
    }

    /**
     * Test set / get user.
     */
    public function testCanSetAndGetUser(): void
    {
        $vote = new Vote();
        $this->assertEmpty($vote->getUser());
        $testUser = new User();
        $vote->setUser($testUser);
        $this->assertSame($testUser, $vote->getUser());
    }
}
