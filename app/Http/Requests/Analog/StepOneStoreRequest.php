<?php

namespace App\Http\Requests\Analog;

use Illuminate\Foundation\Http\FormRequest;

class StepOneStoreRequest extends FormRequest
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
//        dd($this->all());
        return [
            'front'        => ['required','image'],
            'back'         => ['required','image'],
            'selfie'       => ['required','image'],
        ];
    }

    public function messages()
    {
        return [
            'front' => 'Front side is required',
            'back' => 'Back side is required',
            'selfie' => 'Selfie side is required',
        ];
    }

    protected $stopOnFirstFailure = true;
}
