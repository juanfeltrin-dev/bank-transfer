<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class CreateAccountRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' =>'required|string|max:255',
            'email' =>'required|string|email|max:255',
            'document' =>'required|string|max:255',
            'password' =>'required|string|max:255',
            'type' =>'required|integer',
            'balance' =>'required|integer|gt:0',
        ];
    }
}
