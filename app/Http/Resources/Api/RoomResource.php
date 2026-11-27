<?php
namespace App\Http\Resources\Api;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class RoomResource extends JsonResource { public function toArray(Request $request): array { return ['id'=>$this->id,'name'=>$this->name,'building'=>$this->building,'floor'=>$this->floor,'images'=>$this->images,'gender'=>$this->gender,'capacity'=>$this->capacity,'size'=>$this->size,'available_from'=>$this->available_from?->toDateString(),'description'=>$this->description,'facilities'=>$this->facilities,'available'=>$this->available,'is_favorite'=>(bool)$this->is_favorite]; } }