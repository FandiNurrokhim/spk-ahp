<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    use HasFactory;

    protected $table = "kriteria";
    protected $primaryKey = "id";
    public $incrementing = "true";
    // protected $keyType = "string";
    public $timestamps = "true";
    protected $fillable = [
        "kode",
        "nama",
        'bobot',
        'jenis'
    ];

    protected static function booted()
    {
        // Event setelah create
        static::created(function ($kriteria) {
            self::reorderKodes();
        });

        // Event setelah update
        static::updated(function ($kriteria) {
            self::reorderKodes();
        });

        // Event setelah delete
        static::deleted(function ($kriteria) {
            self::reorderKodes();
        });
    }

    /**
     * Reorder semua kode kriteria agar selalu urut C1, C2, C3, dst
     */
    public static function reorderKodes()
    {
        // Ambil semua kriteria yang diurutkan berdasarkan ID (urutan pembuatan)
        $kriterias = self::orderBy('id', 'asc')->get();
        
        $counter = 1;
        foreach ($kriterias as $kriteria) {
            $newKode = "C" . $counter;
            
            // Update kode hanya jika berbeda untuk menghindari infinite loop
            if ($kriteria->kode !== $newKode) {
                // Update tanpa trigger event lagi
                self::withoutEvents(function () use ($kriteria, $newKode) {
                    $kriteria->update(['kode' => $newKode]);
                });
            }
            
            $counter++;
        }
    }

    /**
     * Get next available kode
     */
    public static function getNextKode()
    {
        $lastKriteria = self::orderBy('id', 'desc')->first();
        if ($lastKriteria) {
            $allKriterias = self::count();
            return "C" . ($allKriterias + 1);
        }
        return "C1";
    }
}