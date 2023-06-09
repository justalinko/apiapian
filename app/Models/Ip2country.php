<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ip2country extends Model
{
    use HasFactory;

    public function isproxy()
    {
        return $this->hasOne(Isproxy::class);
    }
    public function proxycheck()
    {
        return $this->hasOne(Proxycheck::class);
    }
}
