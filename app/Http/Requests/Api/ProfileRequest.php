<?php
namespace App\Http\Requests\Api;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class ProfileRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array { return ['name'=>'sometimes|required|string|max:255','email'=>['sometimes','required','email','max:255',Rule::unique('users','email')->ignore($this->user()->id)],'nim'=>['sometimes','required','string','max:30',Rule::unique('users','nim')->ignore($this->user()->id)],'phone'=>'sometimes|nullable|string|max:30','study_program'=>'sometimes|nullable|string|max:255','birth_date'=>'sometimes|nullable|date|before:today','gender'=>'sometimes|nullable|string|max:30','address'=>'sometimes|nullable|string|max:2000']; }
}