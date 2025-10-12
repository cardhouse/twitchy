<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Http\FormRequest;

class NewChatRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'display_name' => 'required|string|max:50',
            'username' => 'nullable|string|max:50',
            'message' => 'required|string|max:500',
            'badges' => 'nullable|array',
            'badges.*.name' => 'nullable|string|max:20',
            'platform' => 'nullable|string|in:twitch,discord,irc,youtube',
            'overlay_key' => 'nullable|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
