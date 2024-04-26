<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    //lo siguiente es para poner los datos editables del formulario
    protected $fillable = ['IsActive','IdArea','UnitMeasurement','IdProduct']; //datos editables

    public function products()
    {
        return $this->belongsTo(Product::class, 'IdProduct'); //belongs es el tipo de relacion y IdProduct es el FK en Outputorder
    }

    public function areas()
    {
        return $this->belongsTo(Area::class, 'IdArea'); //belongs es el tipo de relacion y IdArea es el FK en Product
    }

}
