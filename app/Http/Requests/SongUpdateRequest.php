<?php

namespace App\Http\Requests;

use App\Models\Song;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SongUpdateRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            'title' => 'sometimes|required|string|max:255',
            'album_id' => 'nullable|string|max:255',
            'genre_id' => 'sometimes|required|exists:genres,id',
            'contributions' => 'sometimes|array|min:1',
            'contributions.*.artist_id' => 'required|exists:artists,id',
            'contributions.*.role_id' => 'required|exists:roles,id'
        ];
    }
}
