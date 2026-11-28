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
class AdminSecurityTest extends TestCase
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
    public function test_guest_cannot_open_admin(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
    }
    public function test_student_cannot_open_admin(): void
    {
        $this->actingAs($this->student())->get('/admin')->assertForbidden();
    }
    public function test_admin_can_open_dashboard(): void
    {
        $this->actingAs($this->admin())->get('/admin')->assertOk();
    }
    public function test_student_cannot_modify_rooms_via_api(): void
    {
        Sanctum::actingAs($this->student());
        $this->postJson('/api/rooms', [])->assertForbidden();
        $this->putJson('/api/rooms/A101', [])->assertForbidden();
        $this->deleteJson('/api/rooms/A101')->assertForbidden();
    }
    public function test_admin_can_delete_unused_room(): void
    {
        $room = Room::findOrFail('A101')->replicate();
        $room->id = 'TEST-DELETE';
        $room->save();
        $this->actingAs($this->admin())->delete('/admin/rooms/TEST-DELETE')->assertRedirect(route('admin.rooms.index'));
        $this->assertDatabaseMissing('rooms', ['id' => 'TEST-DELETE']);
    }
    public function test_admin_cannot_delete_room_with_bookings(): void
    {
        $this->actingAs($this->admin())->from('/admin/rooms')->delete('/admin/rooms/A101')->assertSessionHasErrors('room');
        $this->assertDatabaseHas('rooms', ['id' => 'A101']);
    }
}
