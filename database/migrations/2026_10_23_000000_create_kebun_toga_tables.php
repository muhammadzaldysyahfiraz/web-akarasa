<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penanam', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->index();
            $table->string('phone')->nullable();
            $table->text('catatan')->nullable();
            $table->string('access_code_hash');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('tanaman', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('nama_ilmiah')->nullable();
            $table->string('kategori');
            $table->text('deskripsi')->nullable();
            $table->text('manfaat')->nullable();
            $table->text('panduan_penanaman')->nullable();
            $table->text('panduan_perawatan')->nullable();
            $table->string('kebutuhan_cahaya');
            $table->string('kebutuhan_air');
            $table->unsignedSmallInteger('min_hari_panen')->nullable();
            $table->unsignedSmallInteger('max_hari_panen')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('aturan_perawatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanaman_id')->constrained('tanaman')->restrictOnDelete();
            $table->string('nama');
            $table->enum('type', ['penyiraman', 'pemupukan', 'pemangkasan', 'inspeksi', 'lainnya']);
            $table->text('instruksi')->nullable();
            $table->unsignedSmallInteger('interval_days')->nullable();
            $table->unsignedSmallInteger('mulai_setelah_hari')->nullable();
            $table->unsignedSmallInteger('berakhir_setelah_hari')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('tanaman_id');
        });

        Schema::create('tanaman_ditanam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penanam_id')->constrained('penanam')->restrictOnDelete();
            $table->foreignId('tanaman_id')->constrained('tanaman')->restrictOnDelete();
            $table->date('ditanam_at');
            $table->unsignedInteger('quantity')->default(1);
            $table->string('location')->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['bertumbuh', 'dipanen', 'mati'])->default('bertumbuh');
            $table->timestamps();
            $table->index('penanam_id');
            $table->index('tanaman_id');
        });

        Schema::create('jadwal_perawatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanaman_ditanam_id')->constrained('tanaman_ditanam')->cascadeOnDelete();
            $table->foreignId('aturan_perawatan_id')->constrained('aturan_perawatan')->restrictOnDelete();
            $table->date('perawatan_at');
            $table->enum('status', ['pending', 'completed', 'missed'])->default('pending');
            $table->timestamps();
            $table->unique(
                ['tanaman_ditanam_id', 'aturan_perawatan_id', 'perawatan_at'],
                'jadwal_perawatan_unique_occurrence',
            );
            $table->index('tanaman_ditanam_id');
            $table->index('aturan_perawatan_id');
            $table->index('perawatan_at');
        });

        Schema::create('aktivitas_perawatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanaman_ditanam_id')->constrained('tanaman_ditanam')->cascadeOnDelete();
            $table->foreignId('jadwal_perawatan_id')->nullable()->constrained('jadwal_perawatan')->nullOnDelete();
            $table->timestamp('performed_at');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->index('tanaman_ditanam_id');
            $table->index('performed_at');
        });

        Schema::create('panen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanaman_ditanam_id')->constrained('tanaman_ditanam')->cascadeOnDelete();
            $table->date('dipanen_at');
            $table->decimal('quantity', 10, 2)->nullable();
            $table->string('unit')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->index('tanaman_ditanam_id');
            $table->index('dipanen_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('panen');
        Schema::dropIfExists('aktivitas_perawatan');
        Schema::dropIfExists('jadwal_perawatan');
        Schema::dropIfExists('tanaman_ditanam');
        Schema::dropIfExists('aturan_perawatan');
        Schema::dropIfExists('tanaman');
        Schema::dropIfExists('penanam');
    }
};
