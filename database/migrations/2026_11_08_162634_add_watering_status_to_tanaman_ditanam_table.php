<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tanaman_ditanam', function (Blueprint $table) {
            $table->boolean('sudah_disiram')->default(false);
            $table->date('terakhir_disiram_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tanaman_ditanam', function (Blueprint $table) {
            $table->dropColumn([
                'sudah_disiram',
                'terakhir_disiram_at',
            ]);
        });
    }
};