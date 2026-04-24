<?php

namespace App\Http\Requests\Notifications;

use Illuminate\Foundation\Http\FormRequest;

class SendNotificationRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,danger',
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
            'action_url' => 'nullable|url',
            'action_text' => 'nullable|string',
        ];
    }
}
