<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable=['pitch_id','date','start_time','end_time'];

    public function pitch()
    {
        return $this->belongsTo(Pitch::class);
    }

}
