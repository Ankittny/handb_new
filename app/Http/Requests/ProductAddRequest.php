<?php

namespace App\Http\Requests;

use App\Traits\CalculatorTrait;
use App\Traits\ResponseHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Validator;

class ProductAddRequest extends Request
{
    use CalculatorTrait, ResponseHandler;

    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'category_id' => 'required',
            'product_type' => 'required',
            'digital_product_type' => 'required_if' . ':' . 'product_type' . ',==,' . 'digital',
            // 'digital_file_ready' => 'required_if' . ':' . 'digital_product_type' . ',==,' . 'ready_product' . '|' . 'mimes' . ':jpg,jpeg,png,gif,zip,pdf',
            'unit' => 'required_if' . ':' . 'product_type' . ',==,' . 'physical',
            'image' => $this->has('existing_thumbnail') ? 'nullable' : 'required',
            'tax' => 'required' . '|' . 'min' . ':0',
            'tax_model' => 'required',
            'unit_price' => 'required' . '|' . 'numeric' . '|' . 'gt' . ':0',
            'discount' => 'required' . '|' . 'gt' . ':-1',
            'weight_grams' => 'required',
            'how_to_use' => 'required',
            'ingredients1' => 'required',
            'features' => 'required',
            'disclaimer' => 'required',
            'return_policy'=>'required',
            'feature_key'=>'required',
            'shipping_cost' => 'required_if' . ':' . 'product_type' . ',==,' . 'physical' . '|' . 'gt' . ':-1',
            'code' => 'required' . '|' . 'regex:/^[a-zA-Z0-9]+$/' . '|' . 'min' . ':6|' . 'max' . ':20|' . 'unique' . ':products',
            'minimum_order_qty' => 'required' . '|' . 'numeric' . '|' . 'min' . ':1',
        ];
    }

    public function messages(): array
    {
        return [
            'image' . '.' . 'required' => translate('product_thumbnail_is_required!'),
            'category_id' . '.' . 'required' => translate('category_is_required!'),
            'unit' . '.' . 'required_if' => translate('unit_is_required!'),
            'weight_grams' . '.' . 'required_if' => translate('weight_grams_is_required!'),
            'how_to_use' . '.' . 'required_if' => translate('how_to_use!'),
            'ingredients1.required' => translate('Ingredients are required!'),
            'features.required' => translate('Features are required!'),
            'disclaimer.required' => translate('Disclaimer field is optional.'),
            'code.max' => translate('please_ensure_your_code_does_not_exceed_20_characters'),
            'code.min' => translate('code_with_a_minimum_length_requirement_of_6_characters'),
            'minimum_order_qty' . '.' . 'required' => translate('minimum_order_quantity_is_required!'),
            'minimum_order_qty' . '.' . 'min' => translate('minimum_order_quantity_must_be_positive!'),
            'return_policy.required' => 'Return policy field is required.',
            'feature_key.required' => 'Feature key field is required.',
            // 'digital_file_ready' . '.' . 'required_if' => translate('ready_product_upload_is_required!'),
            // 'digital_file_ready' . '.' . 'mimes' => translate('ready_product_upload_must_be_a_file_of_type') . ':' . 'pdf, zip, jpg, jpeg, png, gif.',
            'digital_product_type' . '.' . 'required_if' => translate('digital_product_type_is_required!'),
            'shipping_cost' . '.' . 'required_if' => translate('shipping_cost_is_required!')
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (!$this->has('colors_active') && !$this->file('images') && !$this->has('existing_images')) {
                    $validator->errors()->add(
                        'images', translate('product_images_is_required') . '!'
                    );
                }

                if (getWebConfig(name: 'product_brand') && empty($this->brand_id)) {
                    $validator->errors()->add(
                        'brand_id', translate('brand_is_required') . '!'
                    );
                }

                if ($this['product_type'] == 'physical' && $this['unit_price'] <= $this->getDiscountAmount(price: $this['unit_price'] ?? 0, discount: $this['discount'], discountType: $this['discount_type'])) {
                    $validator->errors()->add(
                        'unit_price', translate('discount_can_not_be_more_or_equal_to_the_price') . '!'
                    );
                }

                if (is_null($this['name'][array_search('EN', $this['lang'])])) {
                    $validator->errors()->add(
                        'name', translate('name_field_is_required') . '!'
                    );
                }

                $productImagesCount = 0;
                if ($this->has('colors_active') && $this->has('colors') && count($this['colors']) > 0) {
                    foreach ($this['colors'] as $color) {
                        $color_ = str_replace('#', '', $color);
                        $image = 'color_image_' . $color_;
                        if ($this->file($image)) {
                            $productImagesCount++;
                        } else if ($this->has($image)) {
                            $productImagesCount++;
                        }

                    }
                    if ($productImagesCount != count($this['colors'])) {
                        $validator->errors()->add(
                            'images', translate('color_images_is_required') . '!'
                        );
                    }
                }

                if ($this['product_type'] == 'physical' && ($this->has('colors') || ($this->has('choice_attributes') && count($this['choice_attributes']) > 0))) {
                    foreach ($this->all() as $requestKey => $requestValue) {
                        if (str_contains($requestKey, 'sku_')) {
                            if (empty($this[$requestKey])) {
                                $validator->errors()->add(
                                    'sku_error', translate('Variation_SKU_are_required') . '!'
                                );
                            }
                        }
                    }
                }

                if ($this['product_type'] == 'digital') {
                    $digitalProductVariationCount = 0;
                    if ($this['extensions_type'] && count($this['extensions_type']) > 0) {
                        $options = [];
                        foreach ($this['extensions_type'] as $type) {
                            $name = 'extensions_options_' . $type;
                            $my_str = implode('|', $this[$name]);
                            $options[$type] = explode(',', $my_str);
                        }

                        foreach ($options as $arrayKey => $array) {
                            foreach ($array as $key => $value) {
                                if ($value) {
                                    $digitalProductVariationCount++;
                                }
                            }
                        }

                        if ($digitalProductVariationCount == 0) {
                            $validator->errors()->add(
                                'variation_error', translate('Digital_Product_variations_are_required') . '!'
                            );
                        }

                        if ($this['digital_product_type'] == 'ready_product' && empty($this['digital_files'])) {
                            $validator->errors()->add(
                                'files', translate('Digital_files_are_required') . '!'
                            );
                        }

                        if ($this['digital_files'] && $digitalProductVariationCount != count($this['digital_files'])) {
                            $validator->errors()->add(
                                'files', translate('Digital_files_are_required') . '!'
                            );
                        }

                        if ($this->has('digital_product_sku') && empty($this['digital_product_sku'])) {
                            $validator->errors()->add(
                                'sku_error', translate('Digital_SKU_are_required') . '!'
                            );
                        } elseif ($this->has('digital_product_sku') && !empty($this['digital_product_sku'])) {
                            foreach ($this['digital_product_sku'] as $digitalSKU) {
                                if (empty($digitalSKU)) {
                                    $validator->errors()->add(
                                        'sku_error', translate('Digital_SKU_are_required') . '!'
                                    );
                                }
                            }
                        }
                    } else {
                        if ($this['digital_product_type'] == 'ready_product' && empty($this['digital_file_ready'])) {
                            $validator->errors()->add(
                                'files', translate('Digital_files_are_required') . '!'
                            );
                        }
                    }
                }
            }
        ];
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $this->errorProcessor($validator)]));
    }
}
