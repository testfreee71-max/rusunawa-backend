<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Room extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'name', 'building', 'floor', 'images', 'gender', 'capacity', 'size', 'available_from', 'description', 'facilities', 'available'];
    protected function casts(): array
    {
        return ['images' => 'array', 'facilities' => 'array', 'available' => 'boolean', 'available_from' => 'date', 'floor' => 'integer', 'capacity' => 'integer'];
    }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function favorites() { return $this->hasMany(Favorite::class); }
    public function favoritedBy() { return $this->belongsToMany(User::class, 'favorites')->withTimestamps(); }
}
