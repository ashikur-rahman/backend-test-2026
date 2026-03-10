<?php

namespace App\Http\Requests\Backstage\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'level' => [
                Rule::in(['admin', 'download', 'readonly']),
                'required',
            ],
        ];
    }
}
