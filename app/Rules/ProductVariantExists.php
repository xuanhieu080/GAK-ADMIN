<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProductVariantExists implements ValidationRule
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
        preg_match('/items\.(\d+)\.product_variant_id/', $attribute, $matches);
        $index = $matches[1] ?? null;
        $data =  request()->all();
        if ($index !== null && isset($data['items'][$index])) {
            $productId = $data['items'][$index]['product_id'];

            // Kiểm tra xem product_variant_id có thuộc về product_id tương ứng và cả hai có hợp lệ.
            $exists = \DB::table('product_variants')->where('id', $value)
                ->where('product_id', $productId)
                ->exists();
            dd($exists);

            if (!$exists) {
                $fail('The selected product variant is invalid for the given product.');
            }
        }
    }

    public function __construct($productIdField, $productVariantIdField)
    {
        $this->productIdField = $productIdField;
        $this->productVariantIdField = $productVariantIdField;
    }

    public function passes($attribute, $value)
    {
        // $this->productVariantIdField là giá trị của product_variant_id
        // $value là giá trị đang được kiểm tra
        // $attribute là tên của trường (ví dụ: items.0.product_variant_id)

        // Lấy productId tương ứng từ input
        $productId = request()->input($this->productIdField);

        dd($productId);

        return \DB::table('products')
            ->where('id', $productId)
            ->where('product_variant_id', $value)
            ->where('is_active', 1)
            ->exists();
    }

    public function message()
    {
        return 'The selected product variant is invalid for the given product.';
    }
}
