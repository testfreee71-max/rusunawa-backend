<?php
namespace App\Http\Requests\Api;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class BookingRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array { return ['room_id'=>'required|string|exists:rooms,id','start_date'=>'required|date_format:Y-m-d|after_or_equal:today','end_date'=>'required|date_format:Y-m-d|after:start_date','purpose'=>'required|string|max:255','note'=>'nullable|string|max:2000']; }
}