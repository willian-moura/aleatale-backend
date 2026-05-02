<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'turn_id',
        'user_id',
        'sentence_id',
    ];

    public function turn(): BelongsTo
    {
        return $this->belongsTo(Turn::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sentence(): BelongsTo
    {
        return $this->belongsTo(Sentence::class);
    }
}
