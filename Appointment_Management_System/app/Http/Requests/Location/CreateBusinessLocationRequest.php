<?php

namespace App\Http\Requests\Location;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateBusinessLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            'business_code' =>
                'required',

            'location_type' =>
                'required|in:BUSINESS,CLIENT',

            'address' =>
                'required',

            'apartment' =>
                'required',

            'street' =>
                'required',

            'city' =>
                'required',

            'state' =>
                'required',

            'postal_code' =>
                'required',

            'country' =>
                'required',

            'status' =>
                'required|in:active,inactive'

        ];
    }
}
