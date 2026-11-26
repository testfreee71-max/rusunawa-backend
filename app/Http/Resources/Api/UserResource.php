<?php
namespace App\Http\Resources\Api;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class UserResource extends JsonResource { public function toArray(Request $request): array { return ['id'=>$this->id,'name'=>$this->name,'email'=>$this->email,'nim'=>$this->nim,'phone'=>$this->phone,'study_program'=>$this->study_program,'birth_date'=>$this->birth_date?->toDateString(),'gender'=>$this->gender,'address'=>$this->address,'role'=>$this->role]; } }