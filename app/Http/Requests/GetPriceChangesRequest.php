<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetPriceChangesRequest extends FormRequest
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
     * @return array<string, array<mixed>|\Illuminate\Contracts\Validation\ValidationRule|string>
     */
    public function rules(): array
    {
        return [
            'data_interval' => ['integer', 'in:1,5,10,30'],
            'page'          => ['integer', 'min:1'],
            'per_page'      => ['integer', 'min:1', 'max:50'],
        ];
    }
}
