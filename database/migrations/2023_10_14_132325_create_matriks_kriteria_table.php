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
        Schema::create('matriks_perbandingan_subkriteria', function (Blueprint $table) {
            $table->id();
            $table->double('nilai')->nullable();
            $table->foreignId("kriteria_id")->constrained("kriteria", "id");
            $table->foreignId("subkriteria_id")->constrained("sub_kriteria", "id");
            $table->foreignId("subkriteria_id_banding")->constrained("sub_kriteria", "id");
            $table->timestamps();
        });

        Schema::create('matriks_nilai_subkriteria', function (Blueprint $table) {
            $table->id();
            $table->double('nilai');
            $table->foreignId("kriteria_id")->constrained("kriteria", "id");
            $table->foreignId("subkriteria_id")->constrained("sub_kriteria", "id");
            $table->foreignId("subkriteria_id_banding")->constrained("sub_kriteria", "id");
            $table->timestamps();
        });

        Schema::create('matriks_nilai_prioritas_subkriteria', function (Blueprint $table) {
            $table->id();
            $table->double('prioritas');
            $table->foreignId("kriteria_id")->constrained("kriteria", "id");
            $table->foreignId("subkriteria_id")->constrained("sub_kriteria", "id");
            $table->timestamps();
        });

        Schema::create('matriks_penjumlahan_subkriteria', function (Blueprint $table) {
            $table->id();
            $table->double('nilai');
            $table->foreignId("kriteria_id")->constrained("kriteria", "id");
            $table->foreignId("subkriteria_id")->constrained("sub_kriteria", "id");
            $table->foreignId("subkriteria_id_banding")->constrained("sub_kriteria", "id");
            $table->timestamps();
        });

        Schema::create('matriks_penjumlahan_prioritas_subkriteria', function (Blueprint $table) {
            $table->id();
            $table->double('prioritas');
            $table->foreignId("kriteria_id")->constrained("kriteria", "id");
            $table->foreignId("subkriteria_id")->constrained("sub_kriteria", "id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matriks_nilai_prioritas_subkriteria');
        Schema::dropIfExists('matriks_nilai_subkriteria');
        Schema::dropIfExists('matriks_penjumlahan_prioritas_subkriteria');
        Schema::dropIfExists('matriks_penjumlahan_subkriteria');
        Schema::dropIfExists('matriks_perbandingan_subkriteria');
    }
};