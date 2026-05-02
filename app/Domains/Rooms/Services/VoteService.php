<?php

namespace App\Domains\Rooms\Services;

use App\Models\Sentence;
use App\Models\Turn;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Collection;

class VoteService
{
    /**
     * Create a new vote.
     */
    public function create(Turn $turn, User $user, Sentence $sentence): Vote
    {
        return Vote::create([
            'turn_id' => $turn->id,
            'user_id' => $user->id,
            'sentence_id' => $sentence->id,
        ]);
    }

    /**
     * Get a vote by id.
     */
    public function getById(int $id): Vote
    {
        return Vote::findOrFail($id);
    }

    /**
     * Get all votes for a turn.
     */
    public function getByTurn(Turn $turn): Collection
    {
        return $turn->votes()->get();
    }

    /**
     * Get all votes for a sentence.
     */
    public function getBySentence(Sentence $sentence): Collection
    {
        return $sentence->votes()->get();
    }

    /**
     * Delete a vote (soft delete).
     */
    public function delete(Vote $vote): void
    {
        $vote->delete();
    }
}
