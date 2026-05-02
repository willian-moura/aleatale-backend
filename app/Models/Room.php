<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'status',
        'started_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
    ];

    public int $submissionTime = 11;
    public int $intervalTime = 11;
    public int $voteTime = 11;
    public int $resultsTime = 11;
    public int $gameTurns = 2;

    /**
     * Get the users in this room.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(RoomUser::class)
            ->withTimestamps()
            ->withPivot('ready')
            ->withPivot('deleted_at')
            ->wherePivotNull('deleted_at');
    }

    public function getDurationPerTurn(): int
    {
        return $this->submissionTime + $this->intervalTime + $this->voteTime + $this->resultsTime;
    }

    public function getDurationTotal(): int
    {
        return $this->getDurationPerTurn() * $this->gameTurns;
    }
}
