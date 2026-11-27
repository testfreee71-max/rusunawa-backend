<?php
namespace App\Http\Resources\Api;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class BookingResource extends JsonResource { public function toArray(Request $request): array { return ['id'=>(string)$this->id,'room_id'=>$this->room_id,'room'=>RoomResource::make($this->whenLoaded('room')),'status'=>$this->status,'submitted_at'=>$this->submitted_at?->toISOString(),'start_date'=>$this->start_date?->toDateString(),'end_date'=>$this->end_date?->toDateString(),'purpose'=>$this->purpose,'note'=>$this->note ?? '']; } }