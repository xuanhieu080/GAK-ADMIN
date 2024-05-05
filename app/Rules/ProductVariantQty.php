<?php

namespace App\Rules;

use App\Models\Product;
use App\Models\ProductVariant;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProductVariantQty implements ValidationRule
{

    protected $productIdField;
    protected $productVariantIdField;

    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        preg_match('/items\.(\d+)\.qty/', $attribute, $matches);
        $index = $matches[1] ?? null;
        $data = request()->all();
        if ($index !== null && !empty($data['items'][$index]) && !empty($data['items'][$index]['product_variant_id'])) {
            $productVariantId = $data['items'][$index]['product_variant_id'];
            $productId = $data['items'][$index]['product_id'];

            $item = ProductVariant::where('id', $productVariantId)->where('product_id', $productId)->first();

            if (!empty($item) && $item->qty < $value) {
                $fail('Số lượng sản phẩm trong kho không đủ');
            }
        }
        if ($index !== null && !empty($data['items'][$index]) && empty($data['items'][$index]['product_variant_id'])) {
            $productId = $data['items'][$index]['product_id'];
            $item = Product::where('id', $productId)->whereDoesntHave('variants')->first();

            if (!empty($item) && $item->qty < $value) {
                $fail('Số lượng sản phẩm trong kho không đủ');
            }
        }
    }

//    public function __construct($productIdField, $productVariantIdField)
//    {
//        $this->productIdField = $productIdField;
//        $this->productVariantIdField = $productVariantIdField;
//    }
}
