<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, \Laravel\Sanctum\HasApiTokens;
    protected $fillable = ['name', 'email', 'password', 'nim', 'phone', 'study_program', 'birth_date', 'gender', 'address'];
    protected $hidden = ['password', 'remember_token'];
    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'birth_date' => 'date', 'password' => 'hashed'];
    }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function favorites() { return $this->hasMany(Favorite::class); }
    public function favoriteRooms() { return $this->belongsToMany(Room::class, 'favorites')->withTimestamps(); }
    public function appNotifications() { return $this->hasMany(AppNotification::class); }
}
