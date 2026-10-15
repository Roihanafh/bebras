<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatans';

    protected $fillable = [
        'menu_kegiatan_id',
        'tipe',
        'judul',
        'deskripsi',
        'gambar',
        'kota',
        'tanggal_lokasi',
        'speaker',
        'urutan',
        'status_validasi',
        'is_popup',
        'dibuat_oleh',
        'divalidasi_oleh',
        'divalidasi_pada',
        'catatan_validasi',
    ];

    protected $casts = [
        'is_popup' => 'boolean',
    ];

    public function menuKegiatan(): BelongsTo
    {
        return $this->belongsTo(MenuKegiatan::class, 'menu_kegiatan_id');
    }

    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function divalidasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'divalidasi_oleh');
    }

    public function gambarBerita(): HasMany
    {
        return $this->hasMany(GambarBerita::class, 'kegiatan_id')->orderBy('urutan', 'asc');
    }
}
