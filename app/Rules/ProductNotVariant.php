<?php

namespace App\Rules;

use App\Models\Product;
use App\Models\ProductVariant;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProductNotVariant implements ValidationRule
{

    protected $productIdField;
    protected $productVariantIdField;
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        preg_match('/items\.(\d+)\.product_id/', $attribute, $matches);
        $index = $matches[1] ?? null;
        $data =  request()->all();
        if ($index !== null && !empty($data['items'][$index]) && !empty($data['items'][$index]['product_variant_id'])) {
            $productVariantId = $data['items'][$index]['product_variant_id'];

            $exists = ProductVariant::where('id', $productVariantId)->where('product_id', $value)->exists();

            if (!$exists) {
                $fail('Dữ liệu không chính xác');
            }
        } if ($index !== null && !empty($data['items'][$index]) && empty($data['items'][$index]['product_variant_id'])) {
            $exists = Product::where('id', $value)->whereDoesntHave('variants')->exists();

            if (!$exists) {
                $fail('Dữ liệu không chính xác');
            }
        }
    }

//    public function __construct($productIdField, $productVariantIdField)
//    {
//        $this->productIdField = $productIdField;
//        $this->productVariantIdField = $productVariantIdField;
//    }
}
