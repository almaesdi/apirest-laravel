<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Car extends Model
{
    //Linea Innecesaria ya que Laravel sabe a que tabla apunta por el nombre (si es que estamos usando convencion)
    protected $table = 'cars';

    //Relacion
    public function user(){
        return $this->belongsTo(User::class);
    }
}
