<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Booking extends Model
{
    protected $fillable = ['user_id', 'room_id', 'status', 'submitted_at', 'start_date', 'end_date', 'purpose', 'note'];
    protected function casts(): array { return ['submitted_at' => 'datetime', 'start_date' => 'date', 'end_date' => 'date']; }
    public function user() { return $this->belongsTo(User::class); }
    public function room() { return $this->belongsTo(Room::class); }
}
