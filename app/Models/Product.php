<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    //lo siguiente es para poner los datos editables del formulario
    protected $fillable = ['Name','UnitMeasurement','LeadTime']; //datos editables

    public function outputorders() //es para la relacion , en este caso outputorder es la tabla donde se relaciona
    {
        return $this->hasMany(Outputorder::class,'id'); //hasMany es el tipo de relacion y id es la PK en Product 
    }

    public function quotes() //es para la relacion , en este caso quotes es la tabla donde se relaciona
    {
        return $this->hasMany(Quote::class,'id'); //hasMany es el tipo de relacion y id es la PK en Product 
    }

    public function inputorders() //es para la relacion , en este caso inputorder es la tabla donde se relaciona
    {
        return $this->hasMany(Inputorder::class,'id'); //hasMany es el tipo de relacion y id es la PK en Product 
    }

    public function stocks() //es para la relacion , en este caso stock es la tabla donde se relaciona
    {
        return $this->hasMany(Stock::class,'id'); //hasMany es el tipo de relacion y id es la PK en stock 
    }

}
