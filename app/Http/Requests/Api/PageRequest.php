<?php
namespace App\Http\Requests\Api;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class PageRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array { return ['page'=>'sometimes|integer|min:1','per_page'=>'sometimes|integer|min:1|max:100']; }
}