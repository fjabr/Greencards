<?php

namespace App\Http\Requests;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ClaimCouponRequest extends FormRequest {
    public function rules() {
        return [
            'code' => 'required|string',
            'user_id' => 'required|integer',
            'package_id' => 'required|integer',
            'amount' => 'required|numeric'
        ];
    }

    public function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }

    public function messages() {
        return [
            'code.required' => 'Code is required',
            'user_id.required' => 'User is required',
            'package_id.required' => 'Package is required',
            'amount.required' => 'Amount is required'
        ];
    }
}
