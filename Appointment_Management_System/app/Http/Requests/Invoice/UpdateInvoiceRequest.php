<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_code' => ['sometimes', 'exists:businesses,code'],
            'appointment_code' => ['sometimes', 'exists:appointments,code'],
            'subtotal' => ['sometimes', 'numeric', 'min:0'],
            'total' => ['sometimes', 'numeric', 'min:0'],
            'status' => [
                'sometimes',
                Rule::in([
                    'draft',
                    'issued',
                    'paid',
                    'canceled',
                ]),
            ],
            'invoice_date' => ['sometimes', 'date'],
        ];
    }
}
