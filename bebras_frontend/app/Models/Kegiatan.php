<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatans';

    protected $fillable = [
        'menu_kegiatan_id',
        'tipe',
        'status_validasi',
        'is_popup',
        'judul',
        'deskripsi',
        'gambar',
        'kota',
        'tanggal_lokasi',
        'speaker',
        'urutan',
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

    public function gambarBerita(): HasMany
    {
        return $this->hasMany(GambarBerita::class, 'kegiatan_id')->orderBy('urutan', 'asc');
    }

    /**
     * Membersihkan teks HTML / TinyMCE dari tag & entitas HTML
     */
    public function getCleanDeskripsiAttribute(): string
    {
        if (empty($this->deskripsi)) {
            return '';
        }

        // Beri spasi pada tag penutup blok dan <br> agar kata tidak menempel
        $text = preg_replace('/<\/(div|p|h1|h2|h3|h4|h5|h6|li|tr|td|th)>/i', ' ', $this->deskripsi);
        $text = preg_replace('/<br\s*\/?>/i', ' ', $text);

        // Hapus tag HTML
        $cleanText = strip_tags($text);

        // Decode HTML entities (&ndash;, &mdash;, &amp;, &quot;, dll)
        $cleanText = html_entity_decode($cleanText, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Bersihkan non-breaking space dan spasi berlebih
        $cleanText = str_replace(["\xc2\xa0", '&nbsp;'], ' ', $cleanText);
        $cleanText = preg_replace('/\s+/u', ' ', $cleanText);

        return trim($cleanText);
    }

    /**
     * Cuplikan teks bersih dengan batasan karakter
     */
    public function getExcerpt(int $limit = 160): string
    {
        return Str::limit($this->clean_deskripsi, $limit);
    }

    public function getExcerptAttribute(): string
    {
        return $this->getExcerpt(160);
    }
}
