<?php

namespace App\Domains\Rooms\Services;

use App\Models\Sentence;
use App\Models\Turn;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class SentenceService
{
    /**
     * Create a new sentence for a turn.
     */
    public function create(Turn $turn, User $user, string $content): Sentence
    {
        return Sentence::create([
            'turn_id' => $turn->id,
            'user_id' => $user->id,
            'content' => $content,
        ]);
    }

    /**
     * Get a sentence by id.
     */
    public function getById(int $id): Sentence
    {
        return Sentence::findOrFail($id);
    }

    /**
     * Get all sentences for a turn.
     */
    public function getByTurn(Turn $turn): Collection
    {
        return $turn->sentences()->get();
    }

    /**
     * Update a sentence.
     */
    public function update(Sentence $sentence, array $data): Sentence
    {
        $sentence->update($data);

        return $sentence;
    }

    /**
     * Delete a sentence (soft delete).
     */
    public function delete(Sentence $sentence): void
    {
        $sentence->delete();
    }
}
