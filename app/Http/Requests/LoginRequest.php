<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            //
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Trường email là bắt buộc.',
            'email.email' => 'Email không đúng định dạng.',
            'email.exists' => 'Email không tồn tại.',
            'password.required' => 'Trường mật khẩu là bắt buộc.',
            'password.string' => 'Mật khẩu phải là một chuỗi.',
        ];
    }
}
