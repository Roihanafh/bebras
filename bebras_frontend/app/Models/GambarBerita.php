<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GambarBerita extends Model
{
    use HasFactory;

    protected $table = 'gambar_berita';

    protected $fillable = [
        'kegiatan_id',
        'gambar',
        'urutan',
        'caption',
    ];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }
}
