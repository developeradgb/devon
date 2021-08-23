<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlayerStoreRequest extends FormRequest
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
            'firstName' => ['required', 'max:255', 'string'],
            'lastName' => ['required', 'max:255', 'string'],
            'playerImageURI' => ['required', 'image'],
            'team_id' => ['required', 'exists:teams,id'],
        ];
    }
}
