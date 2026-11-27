<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\{RegisterRequest,LoginRequest,ProfileRequest,PasswordRequest,BookingRequest,FavoriteRequest,RoomListRequest,PageRequest,RoomRequest};
use App\Http\Resources\Api\{UserResource,RoomResource,BookingResource,NotificationResource};
use App\Models\{User,Room,Booking,Favorite};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB,Hash};
use Illuminate\Validation\ValidationException;

class RusunawaController extends Controller {

    private function ok($message, $data=null, $code=200, $meta=null) {
        $body=['status'=>'success','message'=>$message,'data'=>$data];
        if ($meta!==null) $body['meta']=$meta;
        return response()->json($body,$code);
    }

    private function paged($query,$request,$resource,$message) {
        $p=$query->paginate((int)$request->input('per_page',20));
        return $this->ok($message,$resource::collection($p->items())->resolve($request),200,[
            'current_page'=>$p->currentPage(),
            'last_page'=>$p->lastPage(),
            'per_page'=>$p->perPage(),
            'total'=>$p->total(),
        ]);
    }

    private function roomsFor($user) {
        return Room::query()->withExists([
            'favorites as is_favorite'=>fn($q)=>$q->where('user_id',$user->id)
        ]);
    }

    public function register(RegisterRequest $r) {
        $v=$r->validated();
        unset($v['password_confirmation']);
        $u=User::create($v);

        return $this->ok('Registrasi berhasil',[
            'user'=>new UserResource($u),
            'token'=>$u->createToken('flutter')->plainTextToken
        ],201);
    }

    public function login(LoginRequest $r) {
        $v=$r->validated();

        $u=User::where('email',$v['identifier'])
            ->orWhere('nim',$v['identifier'])
            ->first();

        if (!$u || !Hash::check($v['password'],$u->password)) {
            throw ValidationException::withMessages([
                'identifier'=>['NIM/email atau password salah.']
            ]);
        }

        return $this->ok('Login berhasil',[
            'user'=>new UserResource($u),
            'token'=>$u->createToken('flutter')->plainTextToken
        ]);
    }

    public function logout(Request $r) {
        $r->user()->currentAccessToken()->delete();
        return $this->ok('Logout berhasil');
    }

    public function profile(Request $r) {
        return $this->ok(
            'Profil pengguna',
            new UserResource($r->user())
        );
    }

    public function updateProfile(ProfileRequest $r) {
        $r->user()->update($r->validated());

        return $this->ok(
            'Profil diperbarui',
            new UserResource($r->user()->refresh())
        );
    }

    public function password(PasswordRequest $r) {
        $r->user()->update([
            'password'=>$r->validated('password')
        ]);

        $r->user()->tokens()
            ->where('id','!=',$r->user()->currentAccessToken()->id)
            ->delete();

        return $this->ok('Password diperbarui');
    }

    public function rooms(RoomListRequest $r) {
        $q=$this->roomsFor($r->user());

        if ($r->filled('search')) {
            $s=$r->input('search');

            $q->where(fn($x)=>$x
                ->where('name','like',"%$s%")
                ->orWhere('building','like',"%$s%")
            );
        }

        if ($r->filled('gender')) {
            $q->where('gender',$r->input('gender'));
        }

        if ($r->has('available') && $r->input('available')!==null) {
            $q->where('available',$r->boolean('available'));
        }

        return $this->paged(
            $q->orderBy('id'),
            $r,
            RoomResource::class,
            'Daftar kamar'
        );
    }

    public function room(Request $r,string $id) {
        return $this->ok(
            'Detail kamar',
            new RoomResource(
                $this->roomsFor($r->user())->findOrFail($id)
            )
        );
    }

    public function createRoom(RoomRequest $r) {
        return $this->ok(
            'Kamar ditambahkan',
            new RoomResource(Room::create($r->validated())),
            201
        );
    }

    public function updateRoom(RoomRequest $r,string $id) {
        $room=Room::findOrFail($id);
        $room->update($r->validated());

        return $this->ok(
            'Kamar diperbarui',
            new RoomResource($room)
        );
    }

    public function deleteRoom(Request $r,string $id) {
        abort_unless(
            $r->user()->role==='admin',
            403,
            'Akses admin diperlukan.'
        );

        DB::transaction(function() use($id) {
            $room=Room::lockForUpdate()->findOrFail($id);

            abort_if(
                $room->bookings()->exists(),
                409,
                'Kamar memiliki riwayat pemesanan.'
            );

            $room->delete();
        });

        return $this->ok('Kamar dihapus');
    }

    public function bookings(PageRequest $r) {
        return $this->paged(
            $r->user()->bookings()->with('room')->latest('id'),
            $r,
            BookingResource::class,
            'Daftar pemesanan'
        );
    }

    public function booking(Request $r,string $id) {
        return $this->ok(
            'Detail pemesanan',
            new BookingResource(
                $r->user()->bookings()
                    ->with('room')
                    ->findOrFail($id)
            )
        );
    }

    // BUG-02 FIX: Mencegah double booking lintas kamar.
    public function createBooking(BookingRequest $r) {
        $v=$r->validated();

        $b=DB::transaction(function() use($r,$v) {

            // Kunci pengguna agar pemesanan serentak
            // dari pengguna yang sama diproses bergantian.
            User::lockForUpdate()->findOrFail($r->user()->id);

            $room=Room::lockForUpdate()
                ->findOrFail($v['room_id']);

            // Validasi ketersediaan kamar.
            if (
                !$room->available ||
                $v['start_date']<$room->available_from->toDateString()
            ) {
                throw ValidationException::withMessages([
                    'room_id'=>[
                        'Kamar tidak tersedia pada tanggal tersebut.'
                    ]
                ]);
            }

            // FIX PERTEMUAN 11:
            // Periksa seluruh pemesanan aktif pengguna,
            // bukan hanya pemesanan pada kamar yang sama.
            $overlap=Booking::where('user_id',$r->user()->id)
                ->whereIn('status',[
                    'waiting',
                    'processing',
                    'approved'
                ])
                ->where('start_date','<',$v['end_date'])
                ->where('end_date','>',$v['start_date'])
                ->exists();

            if ($overlap) {
                throw ValidationException::withMessages([
                    'start_date'=>[
                        'Anda sudah memiliki pemesanan pada periode tersebut.'
                    ]
                ]);
            }

            // Periksa kapasitas kamar yang dipilih.
            $active=Booking::where('room_id',$room->id)
                ->whereIn('status',[
                    'waiting',
                    'processing',
                    'approved'
                ])
                ->where('start_date','<',$v['end_date'])
                ->where('end_date','>',$v['start_date']);

            if ($active->count()>=$room->capacity) {
                throw ValidationException::withMessages([
                    'room_id'=>[
                        'Kapasitas kamar penuh pada periode tersebut.'
                    ]
                ]);
            }

            return $r->user()->bookings()->create(
                $v+[
                    'status'=>'waiting',
                    'submitted_at'=>now()
                ]
            );
        });

        return $this->ok(
            'Pemesanan berhasil',
            new BookingResource($b->load('room')),
            201
        );
    }

    public function cancelBooking(Request $r,string $id) {
        $b=DB::transaction(function() use($r,$id) {
            $b=$r->user()->bookings()
                ->lockForUpdate()
                ->findOrFail($id);

            abort_unless(
                in_array($b->status,['waiting','processing'],true),
                409,
                'Pemesanan tidak dapat dibatalkan.'
            );

            $b->update(['status'=>'cancelled']);

            return $b;
        });

        return $this->ok(
            'Pemesanan dibatalkan',
            new BookingResource($b->load('room'))
        );
    }

    public function favorites(PageRequest $r) {
        return $this->paged(
            $this->roomsFor($r->user())
                ->whereHas(
                    'favorites',
                    fn($q)=>$q->where('user_id',$r->user()->id)
                )
                ->orderBy('id'),
            $r,
            RoomResource::class,
            'Daftar favorit'
        );
    }

    public function addFavorite(FavoriteRequest $r) {
        $f=Favorite::firstOrCreate([
            'user_id'=>$r->user()->id,
            'room_id'=>$r->validated('room_id')
        ]);

        return $this->ok(
            'Favorit disimpan',
            new RoomResource(
                $this->roomsFor($r->user())
                    ->findOrFail($f->room_id)
            ),
            $f->wasRecentlyCreated?201:200
        );
    }

    public function removeFavorite(Request $r,string $id) {
        $f=$r->user()->favorites()
            ->where('room_id',$id)
            ->firstOrFail();

        $f->delete();

        return $this->ok('Favorit dihapus');
    }

    public function notifications(PageRequest $r) {
        return $this->paged(
            $r->user()->appNotifications()->latest('id'),
            $r,
            NotificationResource::class,
            'Daftar notifikasi'
        );
    }

    public function readNotification(Request $r,string $id) {
        $n=$r->user()->appNotifications()->findOrFail($id);

        $n->update(['is_read'=>true]);

        return $this->ok(
            'Notifikasi dibaca',
            new NotificationResource($n)
        );
    }

    public function readAll(Request $r) {
        $r->user()->appNotifications()
            ->where('is_read',false)
            ->update(['is_read'=>true]);

        return $this->ok('Semua notifikasi dibaca');
    }
}
