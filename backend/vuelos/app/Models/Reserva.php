<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reservations';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'flight_id',
        'status',
        'reserved_at'
    ];

    // Relación: una reserva pertenece a un vuelo
    public function vuelo()
    {
        return $this->belongsTo(Vuelo::class, 'flight_id', 'id');
    }

    // Relación: una reserva pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'user_id', 'id');
    }
}
