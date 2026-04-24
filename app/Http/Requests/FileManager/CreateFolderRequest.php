<?php

namespace App\Http\Requests\FileManager;

use Illuminate\Foundation\Http\FormRequest;

class CreateFolderRequest extends FormRequest
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
            'folder_name' => 'required|string|max:100|regex:/^[a-zA-Z0-9 _.-]+$/',
            'parent_folder' => 'nullable|string',
        ];
    }
}
