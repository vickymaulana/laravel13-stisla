<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && ($user->hasRole('superadmin') || $user->can('manage-settings'));
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'key' => 'required|string|unique:settings,key',
            'label' => 'required|string',
            'value' => 'nullable',
            'type' => 'required|in:text,number,boolean,json,email,url,textarea',
            'group' => 'required|string',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ];
    }
}
