<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nave extends Model
{
    protected $table = 'naves';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'capacity',
        'model'
    ];

    public function vuelos()
    {
        return $this->hasMany(Vuelo::class, 'nave_id', 'id');
    }
}
