<?php

namespace App\Http\Requests;

use App\Rules\Age;
use App\Rules\Step;
use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
        $type = explode('/',request()->path())[0];
        $valid = [
            'firstname'     => ['required', 'regex:/\A(?![ ])(?![ ]*$)[a-zA-Z0-9 ]*(?<![ ])\z/'],
            'middlename'    => ['nullable', 'regex:/\A(?![ ])(?![ ]*$)[a-zA-Z0-9 ]*(?<![ ])\z/'],
            'lastname'      => ['required', 'max:255', 'alpha_dash'],
            'mobile'        => ['required'],
            'address'       => ['required'],
            'dob'           => ['required', new Age()],
//            'tid'           => ['required','string','unique:customers,regulaTid'],
            'photo'         => ['required','array', new Step()],
            'photo.*'       => ['required','image','mimes:jpeg,png,jpg,webp,avif,bmp,tiff'],
        ];

        if($type === 'digital') {
            unset($valid['tid']);
        }

        if($type === 'analog') {
            unset($valid['photo']);
            unset($valid['photo.*']);
        }

        return $valid;
    }

    public function withValidator($validator)
    {
        $validator->sometimes(
            ['email'], ['required', 'unique:customers', 'regex:/^[\w\-\.]+@([\w\-]+\.)+[\w\-]{2,4}$/'], function ($input) {
            return !in_array($input->type, ['update']);
        });
    }

    public function messages() : array
    {
        return [
            'regex' => 'The :attribute field wrong or contains invalid special characters.',
        ];
    }

    public function attributes() : array
    {
        return [
            'dob' => 'Date of Birth',
        ];
    }
}
