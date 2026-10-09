<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Galerija vizualizacija projekta: JSON niz oblika [{"path": "...", "caption": "..."}].
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->json('gallery')->nullable()->after('cover_image');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('gallery');
        });
    }
};
