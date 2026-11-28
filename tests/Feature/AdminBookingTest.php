<?php
namespace Tests\Feature;
use App\Models\User;
use App\Models\Room;
use App\Models\Booking;
use App\Models\AppNotification;
use Database\Seeders\RusunawaSeeder;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
class AdminBookingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (!app()->environment('testing') || config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== ':memory:' || !empty(config('database.connections.sqlite.url'))) {
            throw new \RuntimeException('Testing requires SQLite in-memory.');
        }
        Artisan::call('migrate:fresh');
        $this->seed(RusunawaSeeder::class);
    }
    private function student(int $id = 1): User
    {
        return User::where('email', "student$id@example.test")->firstOrFail();
    }
    private function admin(): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'admin'])->save();
        return $user;
    }
    private function booking(): Booking
    {
        return $this->student()->bookings()->where('room_id', 'A101')->firstOrFail();
    }
    public function test_admin_approval_creates_notification(): void
    {
        $booking = $this->booking();
        $this->actingAs($this->admin())->from('/admin/bookings/'.$booking->id)->patch('/admin/bookings/'.$booking->id, ['status'=>'approved'])->assertSessionHasNoErrors();
        $this->assertSame('approved', $booking->fresh()->status);
        $this->assertDatabaseHas('app_notifications', ['user_id'=>$booking->user_id,'type'=>'booking','message'=>'Pengajuan booking Anda telah disetujui.','is_read'=>false]);
    }
    public function test_unavailable_room_cannot_be_approved(): void
    {
        $booking = $this->booking();
        Room::findOrFail($booking->room_id)->update(['available'=>false]);
        $this->actingAs($this->admin())->from('/admin/bookings/'.$booking->id)->patch('/admin/bookings/'.$booking->id, ['status'=>'approved'])->assertSessionHasErrors('status');
        $this->assertSame('waiting', $booking->fresh()->status);
    }
    public function test_full_room_cannot_be_approved(): void
    {
        $booking = $this->booking();
        Room::findOrFail($booking->room_id)->update(['capacity'=>1]);
        $other = $booking->replicate();
        $other->user_id = $this->student(2)->id;
        $other->status = 'approved';
        $other->save();
        $this->actingAs($this->admin())->from('/admin/bookings/'.$booking->id)->patch('/admin/bookings/'.$booking->id, ['status'=>'approved'])->assertSessionHasErrors('status');
        $this->assertSame('waiting', $booking->fresh()->status);
    }
    public function test_student_cannot_approve_booking(): void
    {
        $booking = $this->booking();
        $this->actingAs($this->student())->patch('/admin/bookings/'.$booking->id, ['status'=>'approved'])->assertForbidden();
        $this->assertSame('waiting', $booking->fresh()->status);
    }
}
