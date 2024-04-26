<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckStockQuantity implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
    }
    public function passes($attribute, $value)
    {
        $idArea = request('IdArea');
        $idProduct = request('IdProduct');

        $stockQuantity = Stock::where('IdArea', $idArea)
                            ->where('IdProduct', $idProduct)
                            ->value('Quantity');

        return $value < $stockQuantity;
    }

    public function message()
    {
        return 'El campo Quantity debe ser menor que la cantidad en stock.';
    }

}
