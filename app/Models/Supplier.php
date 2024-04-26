<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['Identification','Name']; //campos editables

    public function quotes() //es para la relacion , en este caso quote es la tabla donde se relaciona
    {
        return $this->hasMany(Quote::class,'Identification'); //hasMany es el tipo de relacion y id es la PK en quote 
    }

    public function inputorders() //es para la relacion , en este caso inputorder es la tabla donde se relaciona
    {
        return $this->hasMany(Inputorder::class,'Identification'); //hasMany es el tipo de relacion y id es la PK en inputorder 
    }
}
