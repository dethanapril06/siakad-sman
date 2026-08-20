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
        Schema::table('semesters', function (Blueprint $table) {
            $table->boolean('is_rapor_open')->default(false)->after('is_active');
            $table->date('tanggal_rapor')->nullable()->after('is_rapor_open');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('semesters', function (Blueprint $table) {
            $table->dropColumn(['is_rapor_open', 'tanggal_rapor']);
        });
    }
};
