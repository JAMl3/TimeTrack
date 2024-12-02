<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];

        // Add employee-specific validation rules if the user is an employee
        if ($this->user()->employee) {
            $rules = array_merge($rules, [
                'phone' => ['nullable', 'string', 'max:20'],
                'emergency_contact' => ['nullable', 'string', 'max:255'],
                'emergency_phone' => ['nullable', 'string', 'max:20'],
                'address' => ['nullable', 'string', 'max:255'],
            ]);
        }

        return $rules;
    }
}
