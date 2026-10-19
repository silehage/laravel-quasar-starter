<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'orderId' => ['required'],
            'description' => ['nullable'],
            'fulfillmentId' => ['required'],
            'manufacturerId' => ['required'],
            'factoryId' => ['required'],
            'etd' => ['required'],
            'items' => ['required', 'array'],
            'items.*.productLineId' => ['required', 'string'],
            'items.*.quantity' => ['required', 'numeric', 'min:1'],
        ];
    }
}
