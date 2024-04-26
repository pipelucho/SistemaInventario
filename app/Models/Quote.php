<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = ['RequieredDate','Quantity','UnitMeasurement','IdProduct','IdSupplier','IdArea']; //datos editables

    public function products()
    {
        return $this->belongsTo(Product::class, 'IdProduct'); //belongs es el tipo de relacion y IdProduct es el FK en Outputorder
    }
    public function suppliers()
    {
        return $this->belongsTo(Supplier::class, 'IdSupplier'); //belongs es el tipo de relacion y IdSupplier es el FK en Supplier
    }
    public function inputorders() //es para la relacion , en este caso inputorder es la tabla donde se relaciona
    {
        return $this->hasMany(Inputorder::class,'id'); //hasMany es el tipo de relacion y id es la PK en inputorder 
    }

    public function areas()
    {
        return $this->belongsTo(Area::class, 'IdArea'); //belongs es el tipo de relacion y IdArea es el FK en Product
    }
}
