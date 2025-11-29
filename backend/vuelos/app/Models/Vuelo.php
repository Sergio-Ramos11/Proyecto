<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vuelo extends Model
{
    protected $table = 'flights';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nave_id',
        'origin',
        'destination',
        'departure',
        'arrival',
        'price'
    ];


    public function nave()
    {
        return $this->belongsTo(Nave::class, 'nave_id', 'id');
    }


    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'flight_id', 'id');
    }
}