<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pitch extends Model
{
    use HasFactory;

    protected $fillable=['stadium_id','name'];

    public function Stadium(): BelongsTo
    {
        return $this->belongsTo(Stadium::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
