<?php
namespace App\Http\Requests\Api;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class LoginRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array { return ['identifier'=>'required|string|max:255','password'=>'required|string|max:72']; }
}