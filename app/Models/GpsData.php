<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GpsData extends Model
{
    use HasFactory;

    // Nama tabel secara eksplisit (opsional jika nama file sudah jamak)
    protected $table = 'gps_data';

    /**
     * Kolom yang boleh diisi secara massal.
     * Sesuaikan dengan struktur migrasi yang kita buat tadi.
     */
    protected $fillable = [
        'device_id',
        'latitude',
        'longitude',
        'altitude',
        'speed',
        'satellites',
        'gps_time',
    ];

    /**
     * Casting tipe data agar otomatis menjadi tipe yang benar saat diakses di PHP.
     */
    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double',
        'altitude' => 'float',
        'speed' => 'float',
        'satellites' => 'integer',
        'gps_time' => 'datetime',
    ];

    /**
     * Relasi ke Model Device.
     * Menghubungkan gps_data kembali ke pemiliknya di tabel devices[cite: 1].
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id');
    }
}