<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->string('id', 30)->primary();
            $table->string('name');
            $table->string('building');
            $table->unsignedInteger('floor');
            $table->json('images');
            $table->string('gender', 30);
            $table->unsignedInteger('capacity');
            $table->string('size');
            $table->date('available_from');
            $table->text('description');
            $table->json('facilities');
            $table->boolean('available')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
