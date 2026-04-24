<?php

namespace App\Http\Requests\FileManager;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'original_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'folder' => 'nullable|string',
            'is_public' => 'boolean',
        ];
    }
}
