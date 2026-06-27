<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alternatif extends Model
{
    use HasFactory;

    protected $table = "alternatif";
    protected $primaryKey = "id";
    public $incrementing = "true";
    public $timestamps = "true";
    protected $fillable = [
        "nama",
        'nik',
        'kode',
    ];

    protected static function booted()
    {
        // Event setelah create
        static::created(function ($alternatif) {
            if (!config('app.disable_alternatif_reorder', false)) {
                self::reorderKodes();
            }
        });

        // Event setelah update
        static::updated(function ($alternatif) {
            if (!config('app.disable_alternatif_reorder', false)) {
                self::reorderKodes();
            }
        });

        // Event setelah delete
        static::deleted(function ($alternatif) {
            if (!config('app.disable_alternatif_reorder', false)) {
                self::reorderKodes();
            }
        });
    }

    /**
     * Reorder semua kode alternatif agar selalu urut A1, A2, A3, dst
     */
    public static function reorderKodes()
    {
        // Ambil semua alternatif yang diurutkan berdasarkan ID (urutan pembuatan)
        $alternatifs = self::orderBy('id', 'asc')->get();
        
        $counter = 1;
        foreach ($alternatifs as $alternatif) {
            $newKode = "A" . $counter;
            
            // Update kode hanya jika berbeda untuk menghindari infinite loop
            if ($alternatif->kode !== $newKode) {
                // Update tanpa trigger event lagi
                self::withoutEvents(function () use ($alternatif, $newKode) {
                    $alternatif->update(['kode' => $newKode]);
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
        $lastAlternatif = self::orderBy('id', 'desc')->first();
        if ($lastAlternatif) {
            $allAlternatifs = self::count();
            return "A" . ($allAlternatifs + 1);
        }
        return "A1";
    }
}