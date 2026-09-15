<?php

use App\Enums\UnitStatus;
use App\Enums\UnitType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            $table->foreignId('floor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code');
            $table->string('type')->default(UnitType::Stan->value);
            $table->decimal('area_m2', 8, 2);
            $table->decimal('price', 12, 2)->nullable();
            $table->string('status')->default(UnitStatus::Dostupno->value);
            $table->text('description')->nullable();
            $table->string('floor_plan_image')->nullable();
            $table->json('polygon')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
