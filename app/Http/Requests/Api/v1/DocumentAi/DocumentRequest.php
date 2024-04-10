<?php

namespace App\Http\Requests\Api\v1\DocumentAi;

use App\Http\Requests\Api\v1\AbstractRequest;

class DocumentRequest extends AbstractRequest
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
            'document' => [
                'required',
                'mimes:pdf,gif,tiff,jpeg,png,bmp,webp',
                'max:20480',
            ],
        ];
    }
}
