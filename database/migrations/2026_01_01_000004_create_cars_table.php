<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number', 32)->index();
            $table->string('camera_id', 64)->nullable();
            $table->string('image_path')->nullable();
            $table->timestamp('detected_at')->useCurrent()->index();
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->index(['plate_number', 'detected_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
