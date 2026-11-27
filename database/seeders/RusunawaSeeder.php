<?php
namespace Database\Seeders;

use App\Models\{User, Room, Booking, Favorite, AppNotification};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RusunawaSeeder extends Seeder
{
    public function run(): void
    {
        if (!app()->environment('local', 'testing')) {
            throw new \RuntimeException('Demo seeder only for local/testing.');
        }
        DB::transaction(function () {
            $rooms = json_decode(<<<'JSON'
[
  {
    "id": "A101",
    "name": "Kamar Putra A101",
    "building": "Gedung A",
    "gender": "Putra",
    "size": "3 x 3 m",
    "description": "Kamar nyaman untuk mahasiswa dengan fasilitas lengkap.",
    "floor": 1,
    "capacity": 2,
    "images": [
      "https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800",
      "https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?w=800",
      "https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?w=800"
    ],
    "facilities": [
      "Kasur",
      "Meja belajar",
      "Lemari",
      "Stop kontak",
      "AC",
      "WiFi",
      "WC dalam",
      "Air panas"
    ],
    "available": true,
    "available_from": "2027-01-01"
  },
  {
    "id": "A102",
    "name": "Kamar Putra A102",
    "building": "Gedung A",
    "gender": "Putra",
    "size": "3 x 4 m",
    "description": "Hunian kampus yang nyaman dan dekat dengan fasilitas.",
    "floor": 1,
    "capacity": 2,
    "images": [
      "https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?w=800",
      "https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?w=800",
      "https://images.unsplash.com/photo-1540518614846-7eded433c457?w=800"
    ],
    "facilities": [
      "Kasur",
      "Meja belajar",
      "Lemari",
      "Stop kontak",
      "AC",
      "WiFi",
      "WC dalam",
      "Air panas"
    ],
    "available": true,
    "available_from": "2027-01-01"
  },
  {
    "id": "B201",
    "name": "Kamar Putri B201",
    "building": "Gedung B",
    "gender": "Putri",
    "size": "3 x 3 m",
    "description": "Kamar bersih dan nyaman untuk menunjang aktivitas kuliah.",
    "floor": 2,
    "capacity": 2,
    "images": [
      "https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?w=800",
      "https://images.unsplash.com/photo-1540518614846-7eded433c457?w=800",
      "https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800"
    ],
    "facilities": [
      "Kasur",
      "Meja belajar",
      "Lemari",
      "Stop kontak",
      "AC",
      "WiFi",
      "WC dalam",
      "Air panas"
    ],
    "available": true,
    "available_from": "2027-02-01"
  },
  {
    "id": "C103",
    "name": "Kamar Putri C103",
    "building": "Gedung C",
    "gender": "Putri",
    "size": "3 x 3 m",
    "description": "Kamar privat dengan suasana tenang dan fasilitas lengkap.",
    "floor": 1,
    "capacity": 1,
    "images": [
      "https://images.unsplash.com/photo-1540518614846-7eded433c457?w=800",
      "https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800",
      "https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800"
    ],
    "facilities": [
      "Kasur",
      "Meja belajar",
      "Lemari",
      "Stop kontak",
      "AC",
      "WiFi",
      "WC dalam",
      "Air panas"
    ],
    "available": true,
    "available_from": "2027-01-01"
  },
  {
    "id": "D14",
    "name": "Kamar Putra D14",
    "building": "Gedung D",
    "gender": "Putra",
    "size": "3 x 3 m",
    "description": "Kamar nyaman dengan akses mudah ke area kampus.",
    "floor": 1,
    "capacity": 1,
    "images": [
      "https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800",
      "https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800",
      "https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?w=800"
    ],
    "facilities": [
      "Kasur",
      "Meja belajar",
      "Lemari",
      "Stop kontak",
      "AC",
      "WiFi",
      "WC dalam",
      "Air panas"
    ],
    "available": false,
    "available_from": "2027-01-01"
  },
  {
    "id": "D202",
    "name": "Kamar Putri D202",
    "building": "Gedung D",
    "gender": "Putri",
    "size": "3 x 4 m",
    "description": "Hunian mahasiswa dengan lingkungan yang aman dan nyaman.",
    "floor": 2,
    "capacity": 2,
    "images": [
      "https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800",
      "https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?w=800",
      "https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800"
    ],
    "facilities": [
      "Kasur",
      "Meja belajar",
      "Lemari",
      "Stop kontak",
      "AC",
      "WiFi",
      "WC dalam",
      "Air panas"
    ],
    "available": false,
    "available_from": "2027-03-01"
  }
]
JSON, true, 512, JSON_THROW_ON_ERROR);
            foreach ($rooms as $room) {
                Room::firstOrCreate(['id' => $room['id']], $room);
            }
            for ($i = 1; $i <= 5; $i++) {
                $room = $rooms[$i - 1];
                $user = User::firstOrCreate(['email' => "student$i@example.test"], [
                    'name' => "Mahasiswa Demo $i", 'password' => 'RusunawaDemo123!',
                    'nim' => 'DEMO2026' . $i, 'study_program' => 'Informatika',
                    'gender' => $room['gender'] === 'Putra' ? 'Laki-laki' : 'Perempuan',
                ]);
                $start = $room['available_from'];
                Booking::firstOrCreate(['user_id' => $user->id, 'room_id' => $room['id'], 'start_date' => $start], [
                    'status' => $i === 5 ? 'cancelled' : 'waiting', 'submitted_at' => now(),
                    'end_date' => \Illuminate\Support\Carbon::parse($start)->addMonths(3)->toDateString(),
                    'purpose' => 'Tempat tinggal selama kuliah', 'note' => 'Data simulasi',
                ]);
                Favorite::firstOrCreate(['user_id' => $user->id, 'room_id' => $room['id']]);
                AppNotification::firstOrCreate(['user_id' => $user->id, 'title' => 'Pengajuan kamar'], [
                    'message' => $i === 5 ? 'Pengajuan dibatalkan.' : 'Pengajuan kamar Anda sedang menunggu.',
                    'type' => 'processing', 'is_read' => false,
                ]);
            }
        });
    }
}
