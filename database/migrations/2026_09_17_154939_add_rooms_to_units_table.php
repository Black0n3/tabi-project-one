<?php

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
        // Named "room_count" rather than "rooms" -- Unit already has a rooms()
        // relationship (the polygon-drawn prostorije), and a "rooms" column
        // would shadow that relation's magic accessor.
        Schema::table('units', function (Blueprint $table) {
            $table->unsignedTinyInteger('room_count')->nullable()->after('area_m2');
            $table->index('room_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn('room_count');
        });
    }
};
