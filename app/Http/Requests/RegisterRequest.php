<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sex' => 'required | integer | between:1,2',
            'age' => 'numeric|max:150',
            'address' => 'required|max:255',
            'wanderer_name' => 'required|max:255',
            'family_name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'emergency_tel' => 'required | numeric | digits_between:8,11',
            'audio_file' => 'required',
        ];
    }
}
