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
    ];

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
}
