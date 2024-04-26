<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inputorder extends Model
{
    use HasFactory;
    //lo siguiente es para poner los datos editables del formulario
    protected $fillable = ['IdSupplier','IdProduct','Quantity','UnitMeasurement','IdQuote','IdArea','OrderDate']; //datos editables


    public function products() //es para la relacion , en este caso product es la tabla donde se relaciona
    {
        return $this->belongsTo(Product::class,'IdProduct'); //belongsTo es el tipo de relacion y IdProduct es la FK en Product 
    }

    public function suppliers() //es para la relacion , en este caso Supplier es la tabla donde se relaciona
    {
        return $this->belongsTo(Supplier::class,'IdSupplier'); //belongsTo es el tipo de relacion y IdSupplier es la FK en Supplier 
    }

    public function quotes() //es para la relacion , en este caso quote es la tabla donde se relaciona
    {
        return $this->belongsTo(Quote::class,'IdQuote'); //belongsTo es el tipo de relacion y IdQuote es la FK en Quote 
    }

    public function areas()
    {
        return $this->belongsTo(Area::class, 'IdArea'); //belongs es el tipo de relacion y IdArea es el FK en Product
    }


}
