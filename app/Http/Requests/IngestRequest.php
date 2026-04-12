<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IngestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Set true karena kita pakai API Key di Middleware/Controller
    }

    public function rules(): array
    {
        return [
            'api_key' => 'required|string|exists:devices,api_key',
            'jenis_input' => 'required|string|in:kertas,koin',
            'nominal' => 'required|integer|min:0',
        ];
    }
}