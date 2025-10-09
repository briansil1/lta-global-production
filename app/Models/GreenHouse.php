<?php
// app/Models/GreenHouse.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GreenHouse extends Model
{
    protected $table = 'green_house'; // por claridad
    public function region(): BelongsTo { return $this->belongsTo(Region::class); }
}