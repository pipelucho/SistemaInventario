<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outputorder extends Model
{
    use HasFactory;
    //lo siguiente es para poner los datos editables del formulario
    protected $fillable = ['Quantity','UnitMeasurement','IdProduct','IdEmployee','IdArea']; //datos editables

    public function products()
    {
        return $this->belongsTo(Product::class, 'IdProduct'); //belongs es el tipo de relacion y IdProduct es el FK en Outputorder
    }
    public function employees()
    {
        return $this->belongsTo(Employee::class, 'IdEmployee'); //belongs es el tipo de relacion y IdProduct es el FK en Employee
    }

    public function areas()
    {
        return $this->belongsTo(Area::class, 'IdArea'); //belongs es el tipo de relacion y IdArea es el FK en Product
    }

}
