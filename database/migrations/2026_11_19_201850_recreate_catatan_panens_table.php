<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('catatan_panens');

        Schema::create('catatan_panens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tanaman_ditanam_id')->nullable();
            $table->date('tanggal');
            $table->string('catatan');
            $table->boolean('is_reminder')->default(true);
            $table->boolean('is_done')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_panens');
    }
};