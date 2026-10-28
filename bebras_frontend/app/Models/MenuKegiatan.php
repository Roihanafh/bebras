<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuKegiatan extends Model
{
    use HasFactory;

    protected $table = 'menu_kegiatan';

    protected $fillable = [
        'parent_id',
        'nama_menu',
        'slug',
        'judul',
        'body',
        'gambar',
        'url',
        'urutan',
        'template',
    ];

    public function resolveTemplate(): ?string
    {
        $tpl = is_null($this->parent_id) ? $this->template : $this->parent?->resolveTemplate();
        if ($tpl) {
            return $tpl;
        }

        // Fallback berdasarkan slug root menu jika kolom template di DB belum terisi
        $rootSlug = is_null($this->parent_id) ? $this->slug : $this->parent?->slug;
        return match ($rootSlug) {
            'pengumuman-hasil', 'ct-challenge-2023-for-teachers' => 'pengumuman_hasil',
            'bebras-challenge'                                  => 'bebras_challenge',
            'workshop'                                          => 'workshop',
            'statistik-bebras-indonesia-challenge'              => 'statistik',
            default                                             => null,
        };
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuKegiatan::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuKegiatan::class, 'parent_id')->orderBy('urutan');
    }

    public function kegiatans(): HasMany
    {
        return $this->hasMany(Kegiatan::class, 'menu_kegiatan_id')->orderBy('urutan');
    }

    public function pengumumanHasilFiles(): HasMany
    {
        return $this->hasMany(PengumumanHasilFile::class, 'menu_kegiatan_id');
    }
}
