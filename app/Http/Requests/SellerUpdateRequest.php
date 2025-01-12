<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'seller_commission_code' => ['required', 'string', 'min:4']
        ];
    }
}
