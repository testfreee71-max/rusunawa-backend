<?php
namespace App\Http\Requests\Api;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class PasswordRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array { return ['current_password'=>'required|current_password','password'=>'required|string|min:8|max:72|confirmed']; }
}