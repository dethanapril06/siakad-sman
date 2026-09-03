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
        Schema::table('jenis_nilais', function (Blueprint $table) {
            $table->unsignedInteger('bobot')->default(20)->after('nama');
            $table->unsignedInteger('urutan')->default(1)->after('bobot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_nilais', function (Blueprint $table) {
            $table->dropColumn(['bobot', 'urutan']);
        });
    }
};
