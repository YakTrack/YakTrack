<?php

namespace App\Http\Requests;

use App\EvidenceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTestResultEvidenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => [
                'required',
                Rule::enum(EvidenceType::class),
            ],
            'file'    => 'required_if:type,image|image|max:10240', // 10MB max
            'content' => 'required_if:type,text|string',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.required'       => 'The evidence type is required.',
            'type.enum'           => 'The evidence type must be either image or text.',
            'file.required_if'    => 'An image file is required when evidence type is image.',
            'file.image'          => 'The uploaded file must be an image.',
            'file.max'            => 'The image file may not be greater than 10MB.',
            'content.required_if' => 'Content is required when evidence type is text.',
        ];
    }
}
