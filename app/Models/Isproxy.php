<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Isproxy extends Model
{
    use HasFactory;
    public function ip2country()
    {
        return $this->belongsTo(Ip2country::class);
    }
}
