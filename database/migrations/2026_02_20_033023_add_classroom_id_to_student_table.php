<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Tambah kolom classroom_id setelah kolom 'absen'
            $table->foreignId('classroom_id')->nullable()->after('absen')->constrained('classrooms')->nullOnDelete();
        });

        // Catatan: kolom 'class' (string lama) tidak langsung dihapus
        // agar data lama tidak hilang. Hapus manual setelah migrasi data.
        // Jika ingin langsung hapus, uncomment baris berikut:
        Schema::table('students', function (Blueprint $table) {
           $table->dropColumn('class');
         });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['classroom_id']);
            $table->dropColumn('classroom_id');
        });
    }
};
