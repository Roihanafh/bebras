<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengumumanHasilFile extends Model
{
    use HasFactory;

    protected $table = 'pengumuman_hasil_files';

    protected $fillable = [
        'menu_kegiatan_id',
        'kategori',
        'file_path',
        'original_name',
        'file_size',
    ];

    public function menuKegiatan(): BelongsTo
    {
        return $this->belongsTo(MenuKegiatan::class, 'menu_kegiatan_id');
    }
}
