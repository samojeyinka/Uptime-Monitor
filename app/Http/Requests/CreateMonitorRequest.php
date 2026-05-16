<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateMonitorRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'url' => 'required|url|max:2048|unique:monitors|url',
            'check_interval' => 'sometimes|integer|min:1|max:60',
            'threshold' => 'sometimes|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'url.unique' => 'You are already monitoring the url'
        ];
    }

}
