<?php

namespace App\Http\Requests;

use App\Services\GherkinParser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImportAcceptanceCriteriaRequest extends FormRequest
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
            'project_id' => 'required|exists:projects,id',
            'file' => 'required|file|mimes:feature,txt|max:10240', // 10MB max
            'overwrite_existing' => 'boolean',
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
            'project_id.required' => 'A project must be selected.',
            'project_id.exists' => 'The selected project does not exist.',
            'file.required' => 'A Gherkin file must be uploaded.',
            'file.file' => 'The uploaded file is not valid.',
            'file.mimes' => 'The file must be a .feature or .txt file.',
            'file.max' => 'The file size must not exceed 10MB.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->hasFile('file')) {
                $file = $this->file('file');
                $content = file_get_contents($file->getPathname());
                
                $parser = new GherkinParser();
                $validation = $parser->validate($content);
                
                if (!$validation['valid']) {
                    foreach ($validation['errors'] as $error) {
                        $validator->errors()->add('file', $error);
                    }
                }
            }
        });
    }
}

