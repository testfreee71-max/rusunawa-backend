<?php
namespace App\Http\Requests\Api;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class RegisterRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array { return ['name'=>'required|string|max:255','email'=>'required|email|max:255|unique:users,email','nim'=>'required|string|max:30|unique:users,nim','study_program'=>'required|string|max:255','password'=>'required|string|min:8|max:72|confirmed']; }
}