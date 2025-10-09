<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    public function continent() { return $this->belongsTo(Continent::class); }
}