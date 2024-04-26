<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['Identification','Name']; //campos editables

    public function outputorders() //es para la relacion , en este caso outputorder es la tabla donde se relaciona
    {
        return $this->hasMany(Outputorder::class,'id'); //hasMany es el tipo de relacion y id es la PK en Product 
    }
}
