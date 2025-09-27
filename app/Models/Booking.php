<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable=['pitch_id','date','start_time','end_time'];

    public function pitch(): BelongsTo
    {
        return $this->belongsTo(Pitch::class);
    }

}
