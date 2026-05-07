<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'number'      => 'nullable|string|max:255|unique:invoices,number',
            'date'        => 'nullable|date',
            'due_date'    => 'nullable|date',
            'amount'      => 'nullable|numeric|min:0',
            'total_hours' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_paid'     => 'nullable|boolean',
            'is_sent'     => 'nullable|boolean',
            'client_id'   => 'nullable|exists:clients,id',
        ];
    }
}
