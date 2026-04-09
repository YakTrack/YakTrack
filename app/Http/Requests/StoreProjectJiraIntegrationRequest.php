<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectJiraIntegrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'site_host' => ['required', 'string', 'max:255'],
            'account_email' => ['required', 'email', 'max:255'],
            'api_token' => ['required', 'string', 'min:8'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $host = $this->input('site_host');
        if (! is_string($host)) {
            return;
        }

        $host = trim($host);
        $host = preg_replace('#^https?://#i', '', $host) ?? $host;
        $host = rtrim($host, '/');

        $this->merge([
            'site_host' => $host,
        ]);
    }
}
