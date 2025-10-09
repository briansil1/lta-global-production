<?php
// app/Models/VolumeQuality.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VolumeQuality extends Model
{
    protected $table = 'volume_quality'; // por claridad
    public function region() { return $this->belongsTo(Region::class); }

    // (opcional) scope semántico
    public function scopeTipo($q, $tipo) { return $q->where('tipo', $tipo); }
}



?>