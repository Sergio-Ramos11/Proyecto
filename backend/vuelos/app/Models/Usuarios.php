<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuarios extends Model {
    protected $table = 'users';
    public $timestamps = false;
    protected $fillable = ['name','email','password','role','token'];
}