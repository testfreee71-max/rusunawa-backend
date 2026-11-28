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
class StudentAccessTest extends TestCase
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
    public function test_guest_cannot_access_api_profile(): void
    {
        $this->getJson('/api/profile')->assertUnauthorized();
    }
    public function test_registration_cannot_assign_admin_role(): void
    {
        $this->postJson('/api/register', ['name'=>'Security Test','email'=>'security@example.test','nim'=>'SECURITY-TEST','study_program'=>'Informatika','password'=>'TestPassword123!','password_confirmation'=>'TestPassword123!','role'=>'admin'])->assertCreated();
        $this->assertDatabaseHas('users', ['email'=>'security@example.test','role'=>'student']);
    }
    public function test_profile_update_cannot_assign_admin_role(): void
    {
        $student = $this->student();
        Sanctum::actingAs($student);
        $this->putJson('/api/profile', ['name'=>'Updated Student','role'=>'admin'])->assertOk();
        $this->assertSame('student', $student->fresh()->role);
    }
    public function test_student_cannot_access_another_students_booking(): void
    {
        $booking = $this->student(2)->bookings()->firstOrFail();
        Sanctum::actingAs($this->student());
        $this->getJson('/api/bookings/'.$booking->id)->assertNotFound();
        $this->patchJson('/api/bookings/'.$booking->id.'/cancel')->assertNotFound();
        $this->assertSame($booking->status, $booking->fresh()->status);
    }
    public function test_student_cannot_mark_another_students_notification(): void
    {
        $notification = $this->student(2)->appNotifications()->firstOrFail();
        Sanctum::actingAs($this->student());
        $this->patchJson('/api/notifications/'.$notification->id.'/read')->assertNotFound();
        $this->assertFalse((bool) $notification->fresh()->is_read);
    }
}
