<?php
namespace App\Http\Requests\Api;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class RoomListRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array { return ['search'=>'nullable|string|max:100','gender'=>'nullable|string|max:30','available'=>'nullable|boolean','page'=>'sometimes|integer|min:1','per_page'=>'sometimes|integer|min:1|max:100']; }
}