<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
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
        return [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/|confirmed', // 少なくともアルファベット1文字と数字1文字
            'password_confirmation' => 'required_with:password|string|min:8',
            'display_name' => 'required|string|max:20',
            'daily_cigarettes' => 'required|integer|min:1',
            'pack_cost' => 'required|integer|min:300|max:3000',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => '入力に誤りがあります。',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
