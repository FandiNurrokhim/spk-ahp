<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alternatif', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->unique(); // A1, A2, A3, A4
            $table->timestamps();
        });

        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId("alternatif_id")->constrained("alternatif", "id")->onDelete('cascade');
            $table->foreignId("kriteria_id")->constrained("kriteria", "id")->onDelete('cascade');
            $table->double('nilai');
            $table->timestamps();
        });

        // Ganti hasil_solusi_ahp dengan hasil_wp
        Schema::create('hasil_wp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alternatif_id');
            $table->decimal('vektor_s', 15, 9)->default(0); // Nilai S (produk perpangkatan)
            $table->decimal('vektor_v', 15, 9)->default(0); // Nilai V (preferensi relatif)
            $table->integer('ranking')->default(0); // Ranking berdasarkan nilai V
            $table->timestamps();

            // Foreign key
            $table->foreign('alternatif_id')->references('id')->on('alternatif')->onDelete('cascade');

            // Index untuk performa
            $table->index('ranking');
            $table->index('alternatif_id');
        });

        // Tambahan tabel untuk detail perhitungan WP (opsional)
        Schema::create('detail_wp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alternatif_id');
            $table->unsignedBigInteger('kriteria_id');
            $table->decimal('nilai', 15, 6)->default(0); // Nilai alternatif untuk kriteria ini
            $table->decimal('bobot', 15, 9)->default(0); // Bobot pangkat (wj atau -wj untuk cost)
            $table->decimal('hasil', 15, 9)->default(0); // Hasil pow(nilai, bobot)
            $table->timestamps();

            // Foreign keys
            $table->foreign('alternatif_id')->references('id')->on('alternatif')->onDelete('cascade');
            $table->foreign('kriteria_id')->references('id')->on('kriteria')->onDelete('cascade');

            // Index untuk performa
            $table->index(['alternatif_id', 'kriteria_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_wp');
        Schema::dropIfExists('hasil_wp');
        Schema::dropIfExists('penilaian');
        Schema::dropIfExists('alternatif');
    }
};
