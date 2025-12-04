<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomUser extends Model
{
    use SoftDeletes;

    protected $table = 'room_user';

    protected $fillable = [
        'room_id',
        'user_id',
        'ready',
    ];

    protected function casts(): array
    {
        return [
            'ready' => 'boolean',
        ];
    }

    /**
     * Get the room.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
