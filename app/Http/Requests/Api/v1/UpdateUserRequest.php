<?php

namespace App\Http\Requests\Api\v1;

use App\Models\User;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends AbstractRequest
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
            'name' => ['string', 'max:255'],
            'email' => ['string', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'old_password' => ['required_with:password', 'string', 'min:6', 'max:255'],
            'password' => ['required_with:old_password', 'string', 'min:6', 'max:255', 'confirmed']
        ];
    }
}