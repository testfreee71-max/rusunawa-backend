<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('room_id', 30);
            $table->foreign('room_id')->references('id')->on('rooms')->restrictOnDelete();
            $table->string('status', 30)->default('waiting');
            $table->timestamp('submitted_at')->useCurrent();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('purpose');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['room_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
