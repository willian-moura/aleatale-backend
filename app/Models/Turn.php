<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Turn extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_id',
        'number',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function sentences(): HasMany
    {
        return $this->hasMany(Sentence::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
}
