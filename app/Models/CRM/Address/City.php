<?php

namespace App\Models\CRM\Address;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    public function states() {
        return $this->belongsTo(State::class, 'state_id')->withTrashed();
     }
}
