<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->nullable()->constrained('cars')->nullOnDelete();
            $table->string('plate_number', 32);
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('operator_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('washer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('region_id')->constrained('regions')->restrictOnDelete();
            $table->decimal('price', 12, 2);
            $table->unsignedTinyInteger('status')->default(OrderStatus::NEW->value);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index('plate_number');
            $table->index('status');
            $table->index('created_at');
            $table->index('operator_id');
            $table->index('washer_id');
            $table->index('category_id');
            $table->index('region_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
