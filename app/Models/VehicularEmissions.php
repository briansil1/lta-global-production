<?php
// app/Models/VehicularEmissions.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicularEmissions extends Model
{
    protected $table = 'vehicular_emissions'; // por claridad
    public function region() { return $this->belongsTo(Region::class); }

  
}

