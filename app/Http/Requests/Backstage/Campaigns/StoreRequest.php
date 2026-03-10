<?php

namespace App\Http\Requests\Backstage\Campaigns;

use Illuminate\Foundation\Http\FormRequest;

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
            'timezone' => 'required',
            'starts_at' => 'required',
            'ends_at' => 'required',
        ];
    }
}
