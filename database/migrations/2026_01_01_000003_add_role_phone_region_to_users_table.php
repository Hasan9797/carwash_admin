<?php

declare(strict_types=1);

use App\Enums\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('role')->default(UserRole::OPERATOR->value)->after('email')->index();
            $table->string('phone', 30)->nullable()->after('role');
            $table->foreignId('region_id')->nullable()->after('phone')->constrained('regions')->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('region_id');
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropColumn(['role', 'phone', 'region_id', 'is_active']);
            $table->string('password')->nullable(false)->change();
        });
    }
};
