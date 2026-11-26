<?php
namespace App\Http\Requests\Api;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class RoomRequest extends FormRequest {
    public function authorize(): bool { return $this->user()?->role === 'admin'; }
    public function rules(): array { return ['id'=>$this->isMethod('POST')?'required|string|max:30|unique:rooms,id':'prohibited','name'=>'required|string|max:255','building'=>'required|string|max:255','floor'=>'required|integer|min:0','images'=>'present|array','images.*'=>'url|max:2048','gender'=>'required|string|max:30','capacity'=>'required|integer|min:1','size'=>'required|string|max:255','available_from'=>'required|date_format:Y-m-d','description'=>'required|string|max:5000','facilities'=>'present|array','facilities.*'=>'string|max:255','available'=>'required|boolean']; }
}